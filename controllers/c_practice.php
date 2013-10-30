<?php

class practice_controller extends base_controller {

    public function test7() {

        require(APP_PATH.'/libraries/Image.php');

#	echo APP_PATH."<br>";
#	echo DOC_ROOT."<br>";

	$imageObj = new Image('http://placekitten.com/500/500');

	$imageObj->resize(200,200);

	$imageObj->display();

        echo "You are looking at test1.<br>";

    }
/*
    public function test2() {

        echo Time::now();
    
    }
*/
} #eoc

?>
