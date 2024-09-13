<?php
if (!defined('MECEXEC')) {
    wp_die('Acceso directo no permitido.');
}
$settings = $this->main->get_settings();
$modern_type = '';
if (isset($settings['search_bar_modern_type']) && $settings['search_bar_modern_type'] == '1') {
    $modern_type = 'mec-modern-search-bar ';
}
$output = '<div class="' . esc_attr($modern_type) . ' mec-wrap mec-search-bar-wrap"><form class="mec-search-form mec-totalcal-box" role="search" method="get" id="searchform" action="' . get_bloginfo('url') . '">';

if (isset($settings['search_bar_ajax_mode']) && $settings['search_bar_ajax_mode'] == '1') {
    $output .= '<div class="mec-ajax-search-result">
        <div class="mec-text-input-search"><i class="mec-sl-magnifier"></i><input type="text" placeholder="' . esc_html__('Please enter at least 3 characters', 'modern-events-calendar-lite') . '" value="" id="keyword" name="keyword" />
        </div><div id="mec-ajax-search-result-wrap"><div class="mec-ajax-search-result-events">' . esc_html__('Search results will show here', 'modern-events-calendar-lite') . '</div></div>
    </div>';
} else {
    if (isset($settings['search_bar_text_field']) && $settings['search_bar_text_field'] == '1') {
        $output .= '<div class="mec-text-input-search"><i class="mec-sl-magnifier"></i><input type="search" value="" id="s" name="s" />
        </div>';
    } else {
        $output .= '<input type="hidden" value="" name="s" />';
    }
}

$taxonomies = array(
    'category' => array('key' => 'search_bar_category', 'taxonomy' => 'mec_category', 'icon' => 'folder'),
    'location' => array('key' => 'search_bar_location', 'taxonomy' => 'mec_location', 'icon' => 'location-pin'),
    'organizer' => array('key' => 'search_bar_organizer', 'taxonomy' => 'mec_organizer', 'icon' => 'user'),
    'speaker' => array('key' => 'search_bar_speaker', 'taxonomy' => 'mec_speaker', 'icon' => 'microphone'),
    'tag' => array('key' => 'search_bar_tag', 'taxonomy' => apply_filters('mec_taxonomy_tag', ''), 'icon' => 'tag'),
    'label' => array('key' => 'search_bar_label', 'taxonomy' => 'mec_label', 'icon' => 'pin'),
);

$should_render_dropdown = false;
foreach ($taxonomies as $tax) {
    if (isset($settings[$tax['key']]) && $settings[$tax['key']] == '1') {
        $should_render_dropdown = true;
        break;
    }
}

if ($should_render_dropdown) {
    $output .= '<div class="mec-dropdown-wrap">';
    foreach ($taxonomies as $tax) {
        if (isset($settings[$tax['key']]) && $settings[$tax['key']] == '1') {
            $output .= $this->show_taxonomy($tax['taxonomy'], $tax['icon']);
        }
    }
    $output .= '</div>';
}

$output .= '<input type="text" name="fecha" id="datepicker" autocomplete="off" style="display:none;">
<button type="button" class="mec-icon-calendario" id="calendario"></button>';
$output .= '<input class="mec-search-bar-input" id="mec-search-bar-input" type="submit" alt="' . esc_html__('Buscar', 'modern-events-calendar-lite') . '" value="' . esc_html__('Buscar', 'modern-events-calendar-lite') . '" /><input type="hidden" name="post_type" value="mec-events">';
$output .= '</form></div>';
?>
<?= MEC_kses::form($output); ?>
