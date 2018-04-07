<?php
$pagetype = 'userpage';
$title = 'Edit Profile';
require '../login/misc/pagehead.php';
$uid = $_SESSION['uid'];
$usr = profileData::pullAllUserInfo($uid);
if (@get_headers($usr['userimage'])[0] == 'HTTP/1.1 404 Not Found' || $usr['userimage'] == '') {
    $imgpath = "no_user.jpg";
} else {
    $imgpath = $usr['userimage'];
}
?>
</head>
<body>
    <div class="container">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <h2><?php echo $title;?></h2>
            <form id="profileForm" enctype="multipart/form-data">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <label class="label label-default">Select Your Image</label>
                            <br/>
                            <input type="file" name="userimage" id="userimage" accept="image/*" class="custom-file-input" />
                            <div id="imgholder"> <img id="imgthumb" class="img-thumbnail" src="<?php echo $imgpath."?i=".rand(5, 30000);?>" /> </div>
                            <input id="base64image" hidden></input>
                            <br/> </div>

                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <div id="message"></div>
                            <button type="submit" class="btn btn-primary" id="submitbtn">Save Changes</button>  </div>
                    </div>
            </form>
            </div>
        </div>
        <div class="col-sm-2"></div>
    </div>
</body>

</html>
