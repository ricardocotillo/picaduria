<?php
/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

/**
 * If you are installing Timber as a Composer dependency in your theme, you'll need this block
 * to load your dependencies and initialize Timber. If you are using Timber via the WordPress.org
 * plug-in, you can safely delete this block.
 */

use Timber\Timber;
use Timber\Site;
use Timber\Menu;
use Carbon_Fields\Carbon_Fields;
use Carbon_Fields\Container;
use Carbon_Fields\Field;

$timber = new Timber();

function timber_set_product( $post ) {
    global $product;

    if ( is_woocommerce() ) {
        $product = wc_get_product( $post->ID );
    }
}

/**
 * This ensures that Timber is loaded and available as a PHP class.
 * If not, it gives an error message to help direct developers on where to activate
 */
if ( ! class_exists( 'Timber' ) ) {

	add_action(
		'admin_notices',
		function() {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		}
	);

	add_filter(
		'template_include',
		function( $template ) {
			return get_stylesheet_directory() . '/static/no-timber.html';
		}
	);
	return;
}

/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = array( 'templates', 'views' );

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;


/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class StarterSite extends Site {
	/** Add timber support. */
	public function __construct() {
		add_action( 'after_setup_theme', array($this, 'crb_load') );
		add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
		add_filter( 'timber/context', array( $this, 'add_to_context' ) );
		add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		add_action( 'init', array( $this, 'hide_price_add_cart' ) );
		add_filter( 'use_block_editor_for_post_type', array( $this, 'disable_gutenberg_editor' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ) );
		add_filter( 'woocommerce_enqueue_styles', '__return_false' );
		add_filter( 'script_loader_tag', array($this, 'defer_parsing_of_js') );
		add_action( 'carbon_fields_register_fields', array($this, 'rc_create_fields') );
		add_filter( 'admin_init', array( $this, 'rc_disable_editor' ) );

		parent::__construct();
	}
	/** This is where you can register custom post types. */
	public function register_post_types() {

	}
	/** This is where you can register custom taxonomies. */
	public function register_taxonomies() {

	}
	// Disable gutembreg
	public function disable_gutenberg_editor() {
		return false;
	}

	public function rc_disable_editor() {
		if (isset($_GET['post'])) {
			$post_ID = $_GET['post'];
		} else if (isset($_POST['post_ID'])) {
			$post_ID = $_POST['post_ID'];
		}

		if (!isset($post_ID) || empty($post_ID)) {
			return;
		}
		
		if ($post_ID == 18) {
			remove_post_type_support('page', 'editor');
		}
	}

	public function add_scripts() {
		wp_enqueue_script( 'rc_rellax_js', 'https://cdn.jsdelivr.net/gh/dixonandmoe/rellax@master/rellax.min.js', null );
		wp_enqueue_script( 'rc_splide_js', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@2.4.21/dist/js/splide.min.js', null );
		wp_enqueue_script( 'rc_waypoints_js', 'https://cdn.jsdelivr.net/npm/waypoints@4.0.1/lib/jquery.waypoints.min.js', null );
		wp_enqueue_script( 'rc_site_js', get_template_directory_uri() . '/static\/site.js', array( 'jquery', 'rc_rellax_js', 'rc_splide_js', 'rc_waypoints_js' ) );
	}

	public function defer_parsing_of_js( $url ) {
		if ( is_admin() ) return $url; //don't break WP Admin
		if ( FALSE === strpos( $url, '.js' ) ) return $url;
		if ( strpos( $url, 'jquery.js' ) ) return $url;
		return str_replace( ' src', ' defer src', $url );
	}

	public function hide_price_add_cart() {
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	}

	public function crb_load() {
		Carbon_Fields::boot();
	}

	public function rc_create_fields() {
		Container::make( 'theme_options', 'rc-social-links', __( 'Social Links' ) )
			->add_fields( array(
				Field::make( 'text', 'crb_facebook_link', _( 'Facebook Link' ) )->set_default_value( '#' ),
				Field::make( 'text', 'crb_instagram_link', _( 'Instagram Link' ) )->set_default_value( '#' ),
				Field::make( 'text', 'crb_whatsapp_link', _( 'Whatsapp Link' ) )->set_default_value( '#' ),
			) );

		Container::make( 'post_meta', 'Page Blocks' )
		->where( 'post_type', '=', 'page' )
		->where( 'post_id', '=', 18 )
		->add_fields( array(
			Field::make( 'complex', 'rc_slider', __( 'Slider' ) )
				->add_fields( 'slider', array( 
					Field::make( 'text', 'title', __( 'Title' ) ),
					Field::make( 'image', 'image', __( 'Image' ) )->set_value_type( 'url' ),
					Field::make( 'color', 'color', __( 'Color' ) )->set_palette( array( 'rgb(252, 23, 133)', 'rgb(101, 253, 48)', 'rgb(38, 218, 253)', 'rgb(252, 193, 45)' ) ),
				) 
			),
		) );
	}

	/** This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_to_context( $context ) {
		$context['is_front_page'] = is_front_page();
		$context['menu']  = new Menu( 'header-menu' );
		$context['footer_menu']  = new Menu( 'footer-menu' );
		$context['facebook'] = carbon_get_theme_option( 'crb_facebook_link' );
		$context['instagram'] = carbon_get_theme_option( 'crb_instagram_link' );
		$context['whatsapp'] = carbon_get_theme_option( 'crb_whatsapp_link' );
		$context['site']  = $this;
		$context['custom_logo_url'] = wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' );;
		return $context;
	}

	public function theme_supports() {
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support(
			'post-formats',
			array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
				'gallery',
				'audio',
			)
		);

		add_theme_support( 'menus' );

		// Add Logo support

		$defaults = array(
			'height'               => 400,
			'width'                => 400,
			'flex-height'          => true,
			'flex-width'           => true,
			'header-text'          => array( 'site-title', 'site-description' ),
			'unlink-homepage-logo' => true, 
		);
	 
		add_theme_support( 'custom-logo', $defaults );

		add_theme_support( 'woocommerce', array(
			'thumbnail_image_width' => 300,
		) );
	}

	/** This Would return 'foo bar!'.
	 *
	 * @param string $text being 'foo', then returned 'foo bar!'.
	 */
	public function myfoo( $text ) {
		$text .= ' bar!';
		return $text;
	}

	/** This is where you can add your own functions to twig.
	 *
	 * @param string $twig get extension.
	 */
	public function add_to_twig( $twig ) {
		$twig->addExtension( new Twig\Extension\StringLoaderExtension() );
		$twig->addFilter( new Twig\TwigFilter( 'myfoo', array( $this, 'myfoo' ) ) );
		// $twig->addFunction( new Timber\Twig_Function( 'is_front_page', 'is_front_page' ) );
		return $twig;
	}

}

new StarterSite();

// Admin 
function rc_admin_style() {
	?>
		<style type="text/css">
			#login h1 a {
				background-image: url(<?php echo get_template_directory_uri() ?>/static/img/logo.svg);
				padding: 60px;
				background-size: contain;
			}
			#wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before {
				background-position: center;
				background-size: contain;
				color: rgba(0, 0, 0, 0);
				background-image: url(<?php echo get_template_directory_uri() ?>/static/img/logo_blanco.svg) !important;
			}
		</style>
	<?php
}
add_action( 'admin_enqueue_scripts', 'rc_admin_style' );

function rc_custom_menus() {
	register_nav_menus(
		array(
			'header-menu' => __( 'Header' ),
			'footer-menu' => __( 'Footer' )
		)
	);
}
add_action( 'init', 'rc_custom_menus' );

function rc_send_email_to_admin() {
	$name = $_POST['name'];
	$email = $_POST['email'];
	$message = $_POST['message'];
	$headers = array('Content-Type: text/html; charset=UTF-8');
	$email_message = 'Nuevo mensaje de contacto de '.$name.':<br/><br/>'.$message.'<br/><br/>Puedes contactarte con la persona al correo: '.$email;
	wp_mail( 'ricardo.cotillo@gmail.com', 'Nuevo mensaje de contacto', $email_message, $headers );
	wp_redirect( home_url(  ) );
	exit();
}
add_action( 'admin_post_nopriv_contact_form', 'rc_send_email_to_admin' );
add_action( 'admin_post_contact_form', 'rc_send_email_to_admin' );