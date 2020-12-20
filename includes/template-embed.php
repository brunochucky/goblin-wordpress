<body>
<?php
	$postid = get_query_var( 'goblin' );
	if(isset($postid)):
		$post   = get_post( $postid );
		$output =  apply_filters( 'the_content', $post->post_content );
	else:
		$output = 'Not found';
	endif;






	echo $output; 




?>
</body>