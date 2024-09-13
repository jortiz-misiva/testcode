<?php
if ( !defined( 'ABSPATH' ) ) {	
	//throw new Exception('Acceso directo no permitido.');
	throw new AccesoDirectoNoPermitidoException('Acceso directo no permitido.');
}
class AccesoDirectoNoPermitidoException extends Exception {
    public function __construct($message = '', $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
define( 'HELLO_ELEMENTOR_VERSION', '3.0.1' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

if ( ! function_exists( 'hello_elementor_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup() {
		if ( is_admin() ) {
			hello_maybe_update_theme_version_in_db();
		}

		if ( apply_filters( 'hello_elementor_register_menus', true ) ) {
			register_nav_menus( [ 'menu-1' => esc_html__( 'Header', 'hello-elementor' ) ] );
			register_nav_menus( [ 'menu-2' => esc_html__( 'Footer', 'hello-elementor' ) ] );
		}

		if ( apply_filters( 'hello_elementor_post_type_support', true ) ) {
			add_post_type_support( 'page', 'excerpt' );
		}

		if ( apply_filters( 'hello_elementor_add_theme_support', true ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
					'script',
					'style',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				]
			);

			/*
			 * Editor Style.
			 */
			add_editor_style( 'classic-editor.css' );

			/*
			 * Gutenberg wide images.
			 */
			add_theme_support( 'align-wide' );

			/*
			 * WooCommerce.
			 */
			if ( apply_filters( 'hello_elementor_add_woocommerce_support', true ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'hello_elementor_setup' );

function hello_maybe_update_theme_version_in_db() {
	$theme_version_option_name = 'hello_theme_version';
	// The theme version saved in the database.
	$hello_theme_db_version = get_option( $theme_version_option_name );

	// If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
	if ( ! isset($hello_theme_db_version) || ! $hello_theme_db_version || version_compare( $hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<' ) ) {
		update_option( $theme_version_option_name, HELLO_ELEMENTOR_VERSION );
	}
}

if ( ! function_exists( 'hello_elementor_display_header_footer' ) ) {
	/**
	 * Check whether to display header footer.
	 *
	 * @return bool
	 */
	function hello_elementor_display_header_footer() {
		$hello_elementor_header_footer = true;

		return apply_filters( 'hello_elementor_header_footer', $hello_elementor_header_footer );
	}
}

if ( ! function_exists( 'hello_elementor_scripts_styles' ) ) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles() {
		$min_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( apply_filters( 'hello_elementor_enqueue_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor',
				get_template_directory_uri() . '/style' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( apply_filters( 'hello_elementor_enqueue_theme_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				get_template_directory_uri() . '/theme' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( hello_elementor_display_header_footer() ) {
			wp_enqueue_style(
				'hello-elementor-header-footer',
				get_template_directory_uri() . '/header-footer' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

if ( ! function_exists( 'hello_elementor_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations( $elementor_theme_manager ) {
		if ( apply_filters( 'hello_elementor_register_elementor_locations', true ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action( 'elementor/theme/register_locations', 'hello_elementor_register_elementor_locations' );

if ( ! function_exists( 'hello_elementor_content_width' ) ) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	/*
	function hello_elementor_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'hello_elementor_content_width', 800 );
	}
	*/
}
//add_action( 'after_setup_theme', 'hello_elementor_content_width', 0 );

if ( ! function_exists( 'hello_elementor_add_description_meta_tag' ) ) {
	/**
	 * Add description meta tag with excerpt text.
	 *
	 * @return void
	 */
	function hello_elementor_add_description_meta_tag() {
		if ( ! apply_filters( 'hello_elementor_description_meta_tag', true ) ) {
			return;
		}

		if ( ! is_singular() ) {
			return;
		}

		$post = get_queried_object();
		if ( empty( $post->post_excerpt ) ) {
			return;
		} 

	

	}
}
add_action( 'wp_head', 'hello_elementor_add_description_meta_tag' );

// Admin notice
if ( is_admin() ) {
	require get_template_directory() . '/includes/admin-functions.php';
}

// Settings page
require get_template_directory() . '/includes/settings-functions.php';

// Header & footer styling option, inside Elementor
require get_template_directory() . '/includes/elementor-functions.php';

if ( ! function_exists( 'hello_elementor_customizer' ) ) {
	// Customizer controls
	function hello_elementor_customizer() {
		if ( ! is_customize_preview() ) {
			return;
		}

		if ( ! hello_elementor_display_header_footer() ) {
			return;
		}

		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action( 'init', 'hello_elementor_customizer' );

if ( ! function_exists( 'hello_elementor_check_hide_title' ) ) {
	/**
	 * Check whether to display the page title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter( 'hello_elementor_page_title', 'hello_elementor_check_hide_title' );

/**
 * BC:
 * In v2.7.0 the theme removed the `hello_elementor_body_open()` from `header.php` replacing it with `wp_body_open()`.
 * The following code prevents fatal errors in child themes that still use this function.
 */
if ( ! function_exists( 'hello_elementor_body_open' ) ) {
	function hello_elementor_body_open() {
		wp_body_open();
	}
}

/*******************/
// Asegúrate de no abrir otro tag PHP si ya hay uno abierto.

function enqueue_external_calendar_script() {	    
	wp_enqueue_script('jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js', array('jquery'), null, true);
	wp_enqueue_script('jquery-ui-datepicker-locale', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/i18n/datepicker-es.js', array('jquery-ui'), null, true);	
}

add_action('wp_enqueue_scripts', 'enqueue_external_calendar_script');

function obtener_parametro_get_shortcode() {
	 $args = array(
        'post_type'      => 'mec-events', 
        'post_status'    => 'publish',   
        'posts_per_page' => -1,
    );
    $itemHtml='';
    $query = new WP_Query($args);    
    if ($query->have_posts()) {
		$itemHtml='';
        $itemHtml.='<ul>';        
        while ($query->have_posts()) {
            $query->the_post();            
            $title = get_the_title();            
            $processed_title = do_shortcode('[get_param param="tu_parametro"]') . ' - ' . $title;            
            $itemHtml.= '<li>'.$processed_title.'</li>';
        }
        $itemHtml.= '</ul>';
		?>
		<?=($itemHtml);		
    } else { ?>
	<?= 'No se encontraron posts de tipo mec-events.';
    }    
    wp_reset_postdata();	
}
add_shortcode('get_param', 'obtener_parametro_get_shortcode');

function procesar_posts_mec_events() {
    $args = array(
        'post_type'      => 'mec-events',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
    );   

    $filtro_location = isset($_GET['location']) ? $_GET['location'] : null;
    $filtro_fecha = isset($_GET['fecha']) && $_GET['fecha'] !== '' ? $_GET['fecha'] : null; 

    $query = new WP_Query($args);
    $posts = $query->posts;
    
    if ($query->have_posts()) {
        $html = '<div class="mec-wrap mec-skin-list-container mec-widget" id="mec_skin_575">';
        $html .= '<div class="mec-skin-list-events-container" id="mec_skin_events_575"></div>';
        $html .= '<div class="mec-wrap"><div class="mec-event-list-classic">';
        
        foreach ($posts as $row) {
            $post_ID = $row->ID;
            $title = $row->post_title;
            $thum = obtener_url_thumbnail($post_ID);
            $location = obtener_meta_post_calendario($post_ID, 'mec_location_id');
            $location_arr = get_term($location);
            $location_name = isset($location_arr->name) ? esc_html($location_arr->name) : '';
            $fecha_inicio = obtener_meta_post_calendario($post_ID, 'mec_start_date');
            $fecha_inicio_completo = obtener_meta_post_calendario($post_ID, 'mec_start_datetime');
            $fecha_fin_completo = obtener_meta_post_calendario($post_ID, 'mec_end_datetime');
            
            if (($filtro_location === null || $filtro_location == $location) && ($filtro_fecha === null || $filtro_fecha == $fecha_inicio)) {
                $fechaOriginal = $fecha_inicio;
                $fechaObjeto = date_create_from_format('Y-m-d', $fechaOriginal);
                setlocale(LC_TIME, 'es_ES.UTF-8');
                $fechaFormateada = strftime('%d de %B del %Y', $fechaObjeto->getTimestamp());
                $timestamp_inicio = strtotime($fecha_inicio_completo);
                $timestamp_fin = strtotime($fecha_fin_completo);
                $horainicio = date('H:i', $timestamp_inicio);
                $horafin = date('H:i', $timestamp_fin);
                $zonaHoraria = 'America/Guayaquil';
                $fechaInicioUTC = new DateTime($fecha_inicio_completo, new DateTimeZone($zonaHoraria));
                $fechaInicioUTC->setTimeZone(new DateTimeZone('UTC'));
                $fechaFinUTC = new DateTime($fecha_fin_completo, new DateTimeZone($zonaHoraria));
                $fechaFinUTC->setTimeZone(new DateTimeZone('UTC'));
                $formatoFecha = 'Ymd\THis\Z';
                $fechaInicioStr = $fechaInicioUTC->format($formatoFecha);
                $fechaFinStr = $fechaFinUTC->format($formatoFecha);
                $baseURL = 'https://calendar.google.com/calendar/render';
                $parametros = [
                    'action' => 'TEMPLATE',
                    'text' => urlencode($title),
                    'details' => urlencode(''),
                    'dates' => $fechaInicioStr . '/' . $fechaFinStr
                ];

                $url1 = $baseURL . '?' . http_build_query($parametros);
                $idEvento = $post_ID;
                $fechaOcurrencia = $fecha_inicio;
                $baseURL = site_url('/2024/depratieventos/');
                $parametros2 = [
                    'method' => 'ical',
                    'id' => $idEvento,
                    'occurrence' => $fechaOcurrencia
                ];
                $url2 = $baseURL . '?' . http_build_query($parametros2);
                $html .= '<article class="mec-event-article mec-clear mec-divider-toggle mec-toggle-202405-464" itemscope="">';
                $html .= '<div class="mec-event-image"><a href="' . esc_html($row->guid) . '"><img src="' . esc_html($thum) . '"/></a></div>';
                $html .= '<h4 class="mec-event-title"><a href="' . esc_html($row->guid) . '">' . esc_html($title) . '</a></h4>';
                $html .= '<div class="btn-fecha">' . esc_html($fechaFormateada) . '</div>';
                $html .= '<div class="btn-hora"> De ' . esc_html($horainicio) . ' a ' . esc_html($horafin) . ' </div>';
                $html .= '<div class="btn-lugar">' . (isset($location_name) ? esc_html($location_name) : '') . '</div>';
                $html .= '<div class="btn-agenda-evento" data-ref="' . esc_html($idEvento) . '">Agregar a calendario</div>';
                $html .= '<div class="btn-cal-google-' . esc_html($idEvento) . ' ocultar-grid"><a class="mec-events-gcal mec-events-button mec-color mec-bg-color-hover mec-border-color" href="' . esc_html($url1) . '" target="_blank">Google</a></div>';
                $html .= '<div class="btn-cal-ios-' . esc_html($idEvento) . ' ocultar-grid"><a class="mec-events-gcal mec-events-button mec-color mec-bg-color-hover mec-border-color" href="' . esc_html($url2) . '">iCal / Outlook export</a></div>';
                $html .= '<div class="btn-ver-mas">Conoce más</div>';
                $html .= '</article>';
            }
        }
        $html .= '</div></div></div>';
        echo $html;
    }
    wp_reset_postdata();
}

/*obtención meta*/
function obtener_meta_post_calendario($post_id, $meta_detalle) {
	$valor = get_post_meta($post_id, $meta_detalle, true);
	return $valor;	
}

/*obtención location*/
function obtener_location($post_id) {				
	$valor = get_post_meta($post_id, 'mec_location_id', true);
	return $valor;	
}

/*obtención fecha para el inicio de evento*/
function obtener_start_date($post_id) {				
	$valor = get_post_meta($post_id, 'mec_start_date', true);
	return $valor;	
}

/*obtención imagen*/
function obtener_url_thumbnail($post_id) {				
	$thumbnail_id = get_post_meta($post_id, '_thumbnail_id', true);
	if (isset($thumbnail_id)) {					
		$image_url = wp_get_attachment_image_url($thumbnail_id, 'full');
		return $image_url;
	} else {				
		return 'No hay thumbnail asignado';
	}
}
function listar_mec_events_shortcode() {	
	ob_start();
	if (isset($_GET['location'])){
		procesar_posts_mec_events();
	}    
    return ob_get_clean();
	
}
add_shortcode('listar_mec_events', 'listar_mec_events_shortcode');
