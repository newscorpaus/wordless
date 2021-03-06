<?php
/**
* Provides methods to generate HTML tags programmatically when you can't use a Builder.
*
* @package Wordless
*
* @ingroup helperclass
*/
class TagHelper {
	public function tag_options( $options, $prefix = '' ) {

		$attributes   = array();
		$html_content = array();

		if ( is_array( $options ) ) {

			foreach ( $options as $option_key => $option_value ) {

				if ( is_array( $option_value ) ) {
					if ( 'data' == $option_key ) {
						$attributes[] = $this->tag_options( $option_value, $option_key . '-' );
					} else {
						$html_content[] = $prefix . $option_key . '=' . '"' . addslashes( wp_json_encode( esc_attr( $option_value ) ) ) . '"';
					}
				} else {
					if ( is_null( $option_value ) || ( empty( $option_value ) ) || ( $option_value == $option_key ) ) {
						$html_content[] = esc_attr( $prefix . $option_key );
					} elseif ( is_bool( $option_value ) && ( true == $option_value ) ) {
						$html_content[] = $prefix . $option_key . '=' . '"' . esc_attr( $prefix . $option_key ) . '"';
					} else {
						$html_content[] = $prefix . $option_key . '=' . '"' . esc_attr( $option_value ) . '"';
					}
				}
			}
		} else {
			//We have only a simple string and not an array
			$html_content[] = $options;
		}

		return join( ' ', $html_content );
	}

	/**
	 * Generates an arbitrary HTML tag
	 * @param  string  $name    The tag name
	 * @param  string  $content Content of the tag. If empty the tag will be self-closing
	 * @param  array  $options (optional) Associative array of 'attribute' => 'value' for the HTML tag
	 * @param  boolean $escape  Request a return escaped with htmlentities()
	 * @return string           An HTML tag
	 */
	function content_tag( $name, $content, $options = null, $escape = false ) {

		if ( is_null( $content ) ) {
			$html_content = '<' . sanitize_key( $name );
			$attrs        = $this->tag_options( $options );

			if ( $attrs && ! is_null( $options ) ) {
				$html_content .= ' ' . $attrs;
			}
			$html_content .= '/>';
		} else {
			$html_content = '<' . sanitize_key( $name );
			$attrs        = $this->tag_options( $options );
			if ( ! is_null( $options ) && $attrs ) {
				$html_content .= ' ' . $attrs;
			}
			$html_content .= '>';
			$html_content .= ( (bool) $escape ) ? esc_html( $content ) : $content;
			$html_content .= '</' . sanitize_key( $name ). '>';
		}

		return $html_content;
	}

	/**
	 Generates an <option> HTML tag
	
	 @param string $text The text to be displayed
	 @param string $name The name of the tag
	 @param string $value The value param of the HTML tag
	 @param bool $selected The selected param of the HTML tag
	 @return string HTML <option> tag
*/
	function option_tag( $text, $name, $value, $selected = null ) {
		$options = array(
			'name'  => $name,
			'value' => $value,
		);

		if ( $selected ) {
			$options['selected'] = true;
		}

		return $this->content_tag( 'option', $text, $options );
	}

	/**
	 * Generates an anchor
	 * @param  string $text       The text to be displayed
	 * @param  string $link       (optional) The href param of the <a> tag. defaults to '#'
	 * @param  array $attributes Associative array of 'attribute' => 'value' for the HTML tag
	 * @return string             HTML <a> tag
	 */
	function link_to( $text = '', $link = null, $attributes = null ) {
		if ( ! is_string( $link ) ) {
			$link = '#';
		}

		$options = array( 'href' => esc_url( $link ) );
		if ( is_array( $attributes ) ) {
			$options = array_merge( $options, $attributes );
		}

		return $this->content_tag( 'a', $text, $options );
	}

	/**
	 * Generates a <meta> tag for content type
	 * @param  string $content (optional) Custom content type
	 * @return string          HTML <meta> tag with content type
	 */
	function content_type_meta_tag( $content = null ) {

		$content = $content ? $content : get_bloginfo( 'html_type' ) . '; ' . 'charset=' . get_bloginfo( 'charset' );

		$attrs = array(
			'http-equiv' => 'Content-type',
			'content'    => $content,
		);

		return content_tag( 'meta', null, $attrs );
	}

	/**
	 * Generates the <title> html tag
	 * @param  string $title      (optional) The title of your page
	 * @param  array  $attributes (optional) An associative array of 'attribute' => 'value' for the HTML tag
	 * @return string             HTML <title> tag
	 */
	function title_tag( $title = null, $attributes = array() ) {
		$title = $title ? $title : get_page_title();
		return content_tag( 'title', $title, $attributes );
	}

	/**
	 * Generates a <link> tag for the pingback url
	 * @param  string $url (optional) The pingack url. Defaults to the WP's one
	 * @return string      HTML <link> tag
	 */
	function pingback_link_tag( $url = null ) {
		$url        = $url ? $url : get_bloginfo( 'pingback_url' );
		$attributes = array( 'href' => esc_url( $url ) );
		return content_tag( 'link', null, $attributes );
	}
}

Wordless::register_helper( 'TagHelper' );
