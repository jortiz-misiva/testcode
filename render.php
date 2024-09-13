<?php
if (!defined('MECEXEC')){
    wp_die('Acceso directo no permitido.');
}
/** @var MEC_skin_list $this */
    $styling = $this->main->get_styling();
    $settings = $this->main->get_settings();
    $current_month_divider = isset($_REQUEST['current_month_divider']) ? sanitize_text_field($_REQUEST['current_month_divider']) : 0;
    $display_label = $this->skin_options['display_label'] ?? false;
    $reason_for_cancellation = $this->skin_options['reason_for_cancellation'] ?? false;
    $event_colorskin = (isset($styling['mec_colorskin']) || isset($styling['color'])) ? 'colorskin-custom' : '';
    $map_events = [];
?>
<div class="mec-wrap <?= esc_attr($event_colorskin); ?>">
	<div class="mec-event-list-<?= esc_attr($this->style); ?>">
    <?php foreach($this->events as $date=>$events){
            $month_id = date('Ym', strtotime($date));
            if($this->month_divider && $month_id != $current_month_divider){
                    $current_month_divider = $month_id;?>
                    <div class="mec-month-divider" data-toggle-divider="mec-toggle-<?= date('Ym', strtotime($date)); ?>-<?= esc_attr($this->id); ?>">
                    <span><?= esc_html($this->main->date_i18n('F Y', strtotime($date))); ?></span>
                    <i class="mec-sl-arrow-down"></i>
                </div>
            <?php   }
                foreach($events as $event){
                    $map_events[] = $event;
                    $location_id = $this->main->get_master_location_id($event);
                    $location = ($location_id ? $this->main->get_location_data($location_id) : array());
                    $organizer_id = $this->main->get_master_organizer_id($event);
                    $organizer = ($organizer_id ? $this->main->get_organizer_data($organizer_id) : array());
                    $start_time = (isset($event->data->time) ? $event->data->time['start'] : '');
                    $end_time = (isset($event->data->time) ? $event->data->time['end'] : '');
                    $event_color = $this->get_event_color_dot($event);
                    $event_start_date = !empty($event->date['start']['date']) ? $event->date['start']['date'] : '';
                    $mec_data = $this->display_custom_data($event);
                    $custom_data_class = !empty($mec_data) ? 'mec-custom-data' : '';
                    do_action('mec_schema', $event);
            ?>
            <article class="<?= (isset($event->data->meta['event_past']) && trim($event->data->meta['event_past'])) ? 'mec-past-event ' : ''; ?>mec-event-article <?= esc_attr($custom_data_class); ?> mec-clear <?= esc_attr($this->get_event_classes($event)); ?> mec-divider-toggle mec-toggle-<?= date('Ym', strtotime($date)); ?>-<?= esc_attr($this->id); ?>" itemscope>
            <?php
                $elemStyle = $this->style;
                if ($elemStyle == 'admin'){?>
                    <div class="col-md-2 col-sm-2">
                            <?php if($this->main->is_multipleday_occurrence($event, true)): ?>
                                <div class="mec-event-date">
                                    <div class="event-d mec-color mec-multiple-dates">
                                        <?= esc_html($this->main->date_i18n($this->date_format_modern_1, strtotime($event->date['start']['date']))); ?> -
                                        <?= esc_html($this->main->date_i18n($this->date_format_modern_1, strtotime($event->date['end']['date']))); ?>
                                    </div>
                                    <div class="event-f"><?= esc_html($this->main->date_i18n($this->date_format_modern_2, strtotime($event->date['start']['date']))); ?></div>
                                    <div class="event-da"><?= esc_html($this->main->date_i18n($this->date_format_modern_3, strtotime($event->date['start']['date']))); ?></div>
                                </div>
                            <?php elseif($this->main->is_multipleday_occurrence($event)): ?>
                                <div class="mec-event-date mec-multiple-date-event">
                                    <div class="event-d mec-color"><?= esc_html($this->main->date_i18n($this->date_format_modern_1, strtotime($event->date['start']['date']))); ?></div>
                                    <div class="event-f"><?= esc_html($this->main->date_i18n($this->date_format_modern_2, strtotime($event->date['start']['date']))); ?></div>
                                    <div class="event-da"><?= esc_html($this->main->date_i18n($this->date_format_modern_3, strtotime($event->date['start']['date']))); ?></div>
                                </div>
                                <div class="mec-event-date mec-multiple-date-event">
                                    <div class="event-d mec-color"><?= esc_html($this->main->date_i18n($this->date_format_modern_1, strtotime($event->date['end']['date']))); ?></div>
                                    <div class="event-f"><?= esc_html($this->main->date_i18n($this->date_format_modern_2, strtotime($event->date['end']['date']))); ?></div>
                                    <div class="event-da"><?= esc_html($this->main->date_i18n($this->date_format_modern_3, strtotime($event->date['end']['date']))); ?></div>
                                </div>
                            <?php else: ?>
                                <div class="mec-event-date">
                                    <div class="event-d mec-color"><?= esc_html($this->main->date_i18n($this->date_format_modern_1, strtotime($event->date['start']['date']))); ?></div>
                                    <div class="event-f"><?= esc_html($this->main->date_i18n($this->date_format_modern_2, strtotime($event->date['start']['date']))); ?></div>
                                    <div class="event-da"><?= esc_html($this->main->date_i18n($this->date_format_modern_3, strtotime($event->date['start']['date']))); ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8 col-sm-8">
                            <?php $soldout = $this->main->get_flags($event); ?>
                            <h4 class="mec-event-title">
                                <a class="event-link-admin" rel="noopener" href="<?= esc_url(get_edit_post_link($event->ID)); ?>" target="_blank">
                                    <?= esc_html($event->data->title) ?>
                                </a>
                                <?= esc_html(MEC_kses::element($soldout . $event_color)) ?>
                                <?= esc_html(MEC_kses::element($this->main->get_normal_labels($event, $display_label) . $this->main->display_cancellation_reason($event, $reason_for_cancellation))) ?>
                                <?php do_action('mec_shortcode_virtual_badge', $event->data->ID); ?>
                                <?= esc_html(MEC_kses::element($this->get_label_captions($event, 'mec-fc-style'))) ?>
                            </h4>
                            <div class="mec-event-detail">
                                <div class="mec-event-loc-place">
                                    <?=(isset($location['name']) ? esc_html($location['name']) : '') . (isset($location['address']) && !empty($location['address']) ? ' | '.esc_html($location['address']) : ''); ?>
                                </div>
                                <?php if($this->include_events_times && trim($start_time)){
                                    esc_html(MEC_kses::element($this->main->display_time($start_time, $end_time)));
                                } ?>
                                <?= esc_html(MEC_kses::element($this->display_categories($event))); ?>
                                <?= esc_html(MEC_kses::element($this->display_organizers($event))); ?>
                                <?= esc_html(MEC_kses::element($this->display_cost($event))); ?>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-2">
                            <?php if(isset($event->date['start']['timestamp']) && current_user_can(current_user_can('administrator') ? 'manage_options' : 'mec_bookings') && $total_attendees = $this->main->get_total_attendees_by_event_occurrence($event->data->ID, $event->date['start']['timestamp'])){ ?>
                                <a href="<?= trim($this->main->URL('admin'), '/ ').'/?mec-dl-bookings=1&event_id='.$event->data->ID.'&occurrence='.$event->date['start']['timestamp']; ?>"><?= esc_html__('Download Attendees', 'modern-events-calendar-lite'); ?> (<?= esc_html($total_attendees); ?>)</a>
                            <?php } ?>
                        </div>
                <?php }else{?>
                    <div class="mec-event-image"><?= MEC_kses::element($this->display_link($event, $event->data->thumbnails['full'])); ?></div>
                        <?php if(isset($settings['multiple_day_show_method']) && $settings['multiple_day_show_method'] == 'all_days'){?>
                            <div class="mec-event-date mec-color"><?= $this->icons->display('calendar'); ?> <?= esc_html($this->main->date_i18n($this->date_format_classic_1, strtotime($event->date['start']['date']))); ?></div>
                        <?php }else{ ?>
                            <div class="mec-event-date mec-color"><?= $this->icons->display('calendar'); ?> <?= MEC_kses::element($this->main->dateify($event, $this->date_format_classic_1)); ?></div>
                            <div class="mec-event-time mec-color">
                                <?php if($this->include_events_times && trim($start_time)) { ?>
                                    <?= $this->icons->display('clock'); ?>
                                    <?= MEC_kses::element($this->main->display_time($start_time, $end_time)); ?>
                                <?php } ?></div>
                        <?php } ?>
                        <?= MEC_kses::element($this->get_label_captions($event));
                        $title = $event->data->title;
                        $description = $event->data->content;
                        $start_date = $event->data->meta['mec_start_date'];
                        $hor_start_date = (int)$event->data->meta['mec_start_time_hour'];
                        if ($hor_start_date < 10){
                            $hor_start_date = '0'.$hor_start_date;
                        }
                        $min_start_date = (int)$event->data->meta['mec_start_time_minutes'];
                        if ($min_start_date < 10){
                            $min_start_date = '0'.$min_start_date;
                        }
                        $end_date = $event->data->meta['mec_end_date'];
                        $hor_end_date = (int)$event->data->meta['mec_end_time_hour'];
                        if ($hor_end_date < 10){
                            $hor_end_date = '0'.$hor_end_date;
                        }
                        $min_end_date = (int)$event->data->meta['mec_end_time_minutes'];
                        if ($min_end_date < 10){
                            $min_end_date = '0'.$min_end_date;
                        }
                        $start_time = $start_date.' '.$hor_start_date.':'.$min_start_date;
                        $end_time = $end_date.' '.$hor_end_date.':'.$min_end_date;
                        ?>
                        <?php if ($this->localtime){ ?>
                            <?= MEC_kses::full($this->main->module('local-time.type2', array('event' => $event))); ?>
                        <?php } ?>
                        <h4 class="mec-event-title">
                            <?= MEC_kses::element($this->display_link($event)); ?>
                            <?= MEC_kses::embed($this->display_custom_data($event));?>
                            <?= MEC_kses::element($this->main->get_flags($event), $event_color, $this->main->get_normal_labels($event, $display_label), $this->main->display_cancellation_reason($event, $reason_for_cancellation)); ?>
                            <?php do_action('mec_shortcode_virtual_badge', $event->data->ID ); ?>
                        </h4>
                        <?php if(isset($location['name'])){ ?>
                            <div class="mec-event-detail">
                                <div class="mec-event-loc-place">
                                    <?= $this->icons->display('map-marker'); ?> <?= esc_html($location['name']); ?>
                                </div>
                            </div>
                        <?php } ?>
                        <?= MEC_kses::element($this->display_categories($event)); ?>
                        <?= MEC_kses::element($this->display_organizers($event)); ?>
                        <?= MEC_kses::element($this->display_cost($event)); ?>
                        <?php do_action('mec_list_classic_after_location', $event, $this->skin_options); ?>
                        <?= MEC_kses::form($this->booking_button($event)); ?>
                        <?php
                            $fechaOriginal = $event->date['start']['date'];
                            $fechaObjeto = date_create_from_format('Y-m-d', $fechaOriginal);
                            setlocale(LC_TIME, 'es_ES.UTF-8');
                            $fechaFormateada = strftime('%d de %B del %Y', $fechaObjeto->getTimestamp());
                            $timestamp = $event->date['start']['timestamp'];
                            $horainicio = date('H\hi', $timestamp);
                            $timestamp = $event->date['end']['timestamp'];
                            $horafin = date('H\hi', $timestamp);
                            ?>
                        <div class="btn-fecha"><?= $fechaFormateada; ?></div>
                        <div class="btn-hora"> De <?= $horainicio ?> a <?= $horafin ?> </div>
                        <div class="btn-lugar">
                            <?php if(isset($location['name'])){
                                esc_html($location['name']);
                              } ?>
                        </div>
                    <?php
                        /** @var MEC_main $this */
                        /** @var stdClass $event */
                        $zonaHoraria = 'America/Guayaquil';
                        $fechaInicioUTC = new DateTime($start_time, new DateTimeZone($zonaHoraria));
                        $fechaInicioUTC->setTimeZone(new DateTimeZone('UTC'));
                        $fechaFinUTC = new DateTime($end_time, new DateTimeZone($zonaHoraria));
                        $fechaFinUTC->setTimeZone(new DateTimeZone('UTC'));
                        $formatoFecha = 'Ymd\THis\Z';
                        $fechaInicioStr = $fechaInicioUTC->format($formatoFecha);
                        $fechaFinStr = $fechaFinUTC->format($formatoFecha);
                        $baseURL = 'https://calendar.google.com/calendar/render';
                        $parametros = [
                            'action' => 'TEMPLATE',
                            'text' => urlencode($title),
                            'details' => urlencode($description),
                            'dates' => $fechaInicioStr . '/' . $fechaFinStr
                        ];
                        $url1 = $baseURL.'?'.http_build_query($parametros);
                        /*BLOQUE PARA IOS*/
                        require_once 'wp-load.php';
                        $idEvento = $event->ID;
                        $fechaOcurrencia = $event->data->meta['mec_start_date'];
                        $baseURL = site_url('/2024/depratieventos/');
                        $parametros2 = [
                            'method' => 'ical',
                            'id' => $idEvento,
                            'occurrence' => $fechaOcurrencia
                        ];
                        $url2 = $baseURL.'?'.http_build_query($parametros2);
                    ?>
                        <div class="btn-agenda-evento" data-ref="<?= $event->ID?>">Agregar a calendario</div>
                        <?php if(isset($settings['sn']['googlecal'])){?>
                        <div class="btn-cal-google-<?= $event->ID?> ocultar-grid">
                            <a class="mec-events-gcal mec-events-button mec-color mec-bg-color-hover mec-border-color" href="<?= $url1; ?>" target="_blank" rel="noopener">Google</a>
                        </div>
                        <?php }?>
                        <?php if(isset($settings['sn']['ical'])){ ?>
                        <div class="btn-cal-ios-<?= $event->ID?> ocultar-grid">
                            <a class="mec-events-gcal mec-events-button mec-color mec-bg-color-hover mec-border-color" href="<?= $url2 ?>">iCal / Outlook export</a>
                        </div>
                        <?php } ?>
                        <div class="btn-ver-mas">
                          <?= MEC_kses::element($this->display_link($event,'Conoce más')); ?>
                        </div>
                <?php } ?>
            </article>
		<?php }
        } ?>
	</div>
</div>
