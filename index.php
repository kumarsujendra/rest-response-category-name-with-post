<?php
/**
 * Plugin Name: REST Response Category Name with Post
 * Description: A very simple plugin for development and testing purpose to modify the response of the REST API plugin.
 * Author: Sujendra Kumar
 * Author URI: https://github.com/kumarsujendra/rest-response-category-name-with-post
 */
 
add_action( 'rest_api_init', 'add_category_name' );
 
 
 function add_category_name() {
 
 	$post_types = get_post_types( array( 'public' => true ), 'objects' );

	foreach ( $post_types as $post_type ) {
		$post_type_name     = $post_type->name;
		$show_in_rest       = ( isset( $post_type->show_in_rest ) && $post_type->show_in_rest ) ? true : false;
		if ( function_exists( 'register_rest_field' ) ) {
			register_rest_field( $post_type_name,
				'category_name',
				array(
					'get_callback' => 'get_category_name',
					'schema'       => null,
				)
			);
		} elseif ( function_exists( 'register_api_field' ) ) {
			register_api_field( $post_type_name,
				'category_name',
				array(
					'get_callback' => 'get_category_name',
					'schema'       => null,
				)
			);
		}
	}
    
}


function get_category_name( $object, $field_name, $request ) 
{	
	$taxonomies = wp_list_filter( get_object_taxonomies( $object['type'], 'objects' ), array( 'show_in_rest' => true ) );
	$data = [];
	foreach ( $taxonomies as $taxonomy ) {
		$base = ! empty( $taxonomy->rest_base ) ? $taxonomy->rest_base : $taxonomy->name;
		$terms = get_the_terms( $object['id'], $taxonomy->name );
		//$data[ $base.'_name'] = $terms ? array_values( wp_list_pluck( $terms, 'name' ) ) : array();
		$data = $terms ? array_values( wp_list_pluck( $terms, 'name' ) ) : array();
	}
	
	$customfieldvalue= $data;
	return $customfieldvalue;
}