/* 
 * ajax snippet to send selected value from a drop down to server.
 * add action hook along the lines of:
 * add_action( 'wp_ajax_sket_map_select_handler', array( $this, 'sket_map_select_handlerv' ) );
 * add_action( 'wp_ajax_nopriv_sket_map_select_handler', array( $this, 'sket_map_select_handler' ) );
 */
$.ajax({
    url: sketch_map_data.ajaxurl,
    type: 'post',
    dataType: 'json',
    data: {
        'action': 'sket_map_select_handler',
        'sket_map_ajax_nonce': sketch_map_data.sket_map_ajax_nonce,
        'ajaxurl': sketch_map_data.ajaxurl,
        'sket_map_location': selected_location

    },
    success: function (response) {

        // and populate our results from ajax response
        $embed_wrap.html(response.data);

    },
    error: function (errorThrown) {
        alert('error');
        console.log(errorThrown);
    }
});


