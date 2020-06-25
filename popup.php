<html>

<head>

    <script src="data/main/jquery.js"></script>
    <!--	Able Player dependencies   -->
    <!--    CSS     -->
    <link href='ableplayer/styles/ableplayer.css' type='text/css' rel='stylesheet' />
    <!-- JavaScript -->
    <script src="ableplayer/thirdparty/js.cookie.js"></script>
    <script src="ableplayer/build/ableplayer.js"></script>
    <!--	///// End of Able Player deps   /////-->

    <script type="text/javascript">
        /*$('#audio').bind('canplay', function() {
            //alert('should seek now to ' + <?php //echo $_POST['seek']?>//);
            //AblePlayerInstances[0].seekTo(<?php //echo $_POST['seek']?>//);
        });*/

        AblePlayer.prototype.onMediaNewSourceLoad = function () {
                // seek to last position
                AblePlayerInstances[0].playMedia();
                AblePlayerInstances[0].pauseMedia();
                AblePlayerInstances[0].seekTo(<?php echo $_POST['seek']?>);
        }
    </script>

</head>

<body style="background: #f4f7f8;">

<div id="audio-td" align="right" style="width: 450px">

    <audio id="audio" width="450" data-able-player preload="auto" data-seek-interval="2" src="<?php echo $_POST['src']?>" >
    </audio>


</div>

</body>


</html>
