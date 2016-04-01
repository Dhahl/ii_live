<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" >
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php 
$pageHdrTitle = '<title>'.$book->title.' by '.$book->author.' - Irish Interest - Find Books From or About Ireland</title>';
echo $pageHdrTitle;
?>
<!-- title>Irish Interest - Find Books From or About Ireland</title>  -->
<meta name="description"
	content="Find books from or about Ireland, Search for Irish Books, Irish Writing, Irish Literature, Irish Books by Location, Irish Books by Placename, Irish Books by Townland, Irish Books Area, Irish Authors" />
<meta name="keywords"
	content="Ireland, Irish, Books,  Irish Authors, Writing, New Irish Writing, Irish Literature, Irish Placenames, Irish Townlands" />
<meta name="robots" content="INDEX,FOLLOW" />

<link rel="icon" type="image/png" href="../../favicon.png" />

<!-- Bootstrap -->
<link href="../../css/bootstrap.min.css" rel="stylesheet">
<link href="../../css/bootstrap-theme.min.css" rel="stylesheet">
<link href="../../css/style.css" rel="stylesheet">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body role="document">
<script> /* Google */
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-56305380-2', 'auto');
  ga('send', 'pageview');
	
</script>
			<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

<script src="../../js/bootstrap.min.js"></script>
<script src="../../js/bootstrap-tooltip.js"></script>
<script src="../../js/bootstrap-popover.js"></script>
<script type="text/javascript" src="../../js/bootstrap-progressbar.min.js"></script>
<?php
if( (isset($_SESSION['id']) ) 	&& (!$_SESSION['id']	==	'' ))
	$signedin = true;

if(!isset($_SESSION['accessLevel'])) {
	$userMenu = '<a href="../../login/?publisher=1" title="You must be signed in to activate this option"
  							class="btn btn-custom"
  							style="border-radius: 10px; font-weight:900;">
  							<b>Publisher Log In</b>
  						</a>';
	$sign ='<a href="../../login/">Sign In</a>';
}
else {
	//$userMenu = '<button class="btn btn-custom dropdown-toggle" type="button" data-toggle="dropdown" style="border-radius: 10px;">Publisher Log In</button>';
	$userMenu =	'
   			<button class="btn btn-custom dropdown-toggle" type="button" data-toggle="dropdown"
    				style="border-radius: 10px; font-weight:900;">'
			.$_SESSION['user'].'
    				<span class="caret"></span>
    		</button>';
	$sign =	'<a href="../../logout">Sign Out</a>';

	if($_SESSION['accessLevel']		=='10')	{
		//$userMenu = '<!-- a href="#" class="dropdown-toggle " data-toggle="dropdown" style="padding-left: 0px">'.$_SESSION['user'].'</a> -->
		$userMenu .=
		'<ul class="dropdown-menu" style="text-align:left">
			  	<li><a href="../../admin/">Publishers</a></li>
			  	<li><a href="../../categories/">Categories</a></li>
		 	 	<li><a href="../../banner/">Banners</a></li>
		 	 	<li><a href="../../utilities/">Utilities</a></li>
		 	 	<li class="divider"></li>
  				<li><a href="../../publish/">Add Books / Edit Books</a></li>
  				<li><a href="../../authors/">Author Profiles</a></li>
  				<li><a href="../../profile"><i>'.$_SESSION['user'].'</i> Profile</a></li>
  				<li class="divider"></li>
  				<li><a href="../../logout">Sign Out</a></li>
  			</ul>';
	}
	if( ($_SESSION['accessLevel']		=='1')
			|| ($_SESSION['accessLevel']		== '2'	) )	{
				$userMenu .=	'
					<ul class="dropdown-menu" style="text-align:left">
  					<li><a href="../../publish/">Add Books / Edit Books</a></li>
 					<li><a href="../../authors/">Author Profiles</a></li>
  					<li><a href="../../profile"><i>'.$_SESSION['user'].'</i> Profile</a></li>
  					<li class="divider"></li>
  					<li><a href="../../logout">Sign Out</a></li>
  					</ul>';
			}
			if(($_SESSION['accessLevel']		=='3') || ($_SESSION['accessLevel'] == '4'))	{
				$userMenu .=	'
				<ul class="dropdown-menu" style="text-align:left">
  				<li><a href="../../profile"><i>'.$_SESSION['user'].'</i> Profile</a></li>
  				<li class="divider"></li>
  				<li><a href="../../logout">Sign Out</a></li>
  				</ul>';
			}
}
$authors	= $_SESSION['authors'];
$authors	= json_encode($authors);
$link_A=	 formatAuthors();
$catMenu = '';

foreach($_SESSION['categories'] as $id=>$category)	{
	$catMenu .= '<li style="padding:0px"><a href=../../?category='.$id.' style="padding-top:0px; padding-bottom:0px;">'.$category.'</a></li>';
}

$eventMenu 	= '';
if( isset($_SESSION['accessLevel']) && ($_SESSION['accessLevel'] !== '4') ) {			//	Not "Correspondent" type user
	$eventMenu =  '<li><a href="../../events/" style="padding-top:0px; padding-bottom:0px;">Manage Events</a></li>
							<li class="divider"></li>';
}

foreach($_SESSION['events'] as $id=>$event)	{
	$startDate 	= date('l jS F Y',strtotime($event->date_time_from));
	$startTime 	= date('g:ia',strtotime($event->date_time_from));
	$startingAt 	= $startDate. ' at '.$startTime;
	$endDate 		= date('l jS F Y',strtotime($event->date_time_to));
	$endTime 		= date('g:ia',strtotime($event->date_time_to));
	$endingAt		= $endDate. ' at '.$endTime;
	if($event->eventimage == '') $image = './placeholder.jpg';
	else $image = '../../upload/'.$event->eventimage;
	$authorName = $event->firstname.' '.$event->lastname;

	$eventLink 	=' data-event-title 				= "'.$event->title.'"';
	$eventLink 	.=' data-event-description	= "'.$event->description.'"';
	$eventLink 	.=' data-event-location			= "'.$event->location.'"';
	$eventLink 	.=' data-event-from 				= "'.$startingAt.'"';
	$eventLink 	.=' data-event-to 				= "'.$endingAt.'"';
	$eventLink 	.=' data-event-image 			= "'.$image.'"';
	$eventLink 	.=' data-event-link 				= "'.$event->link.'"';
	$eventLink 	.=' data-event-author 			= "'.$authorName.'"';
	$eventLink 	.=' data-event-book 			= "'.$event->booktitle.'"';

	$eventMenu .='<li><a  href="#event_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal" '.$eventLink.'> '.$event->title.'</a></li>';

}

?>
<a id ="top"></a>
<div class="row" style="background-color:#ffffff; z-index:1000; position:relative" >

	<a href='../../'> 
		<div class="col-md-2 col-sm-2 voffset6" style="background-color:#ffffff; z-index:1000; " >
			<img alt="" src="../../ii_circle.png" class="image-responsive ii_circle" >
		</div>
		<div class="col-md-3  col-sm-4 voffset8 ii_text_div" >
			<img alt="" src="../../IRISH_INTEREST_text.png" class="image-responsive ii_text" >
		</div>
	</a>
	<div class="col-md-5 col-sm-7 col-xs-12 primaryMenu  ">
		<div class="col-md-1 col-sm-1 col-xs-3" style="margin-right:26px; font-size: 16px; font-weight:500;">
			<a href="../../login/&register=register">Join </a>
		</div>
		<div class="col-md-3 col-sm-3 col-xs-3" style="font-size: 16px; font-weight:500;">
			<?php  echo $sign; 
			?>
		</div>
		<div class="col-md-7 col-sm-5 col-xs-6">
			<div class="col-md-8  text-center" style="border: 0px solid; border-radius: 10px; line-height: 2; font-weight: 500; font-size: 16px; font: Impact, Charcoal, sans-serif;">
				<?php echo $userMenu; ?>
			</div>
			<div class="col-md-3 col-sm-2"></div>
		</div>
	</div>
</div>
<!-- div class="banner" style="background-image: url(./DoyleRod.png); height: 500px;"></div> -->
<div style="position:relative">
<!-- Fixed navbar -->
<div class="navbar  " role="navigation" style="z-index:100; margin: 0px; min-height: 0px; border: 0px; padding: 0px">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span> 
			<span class="icon-bar"></span> 
			<span class="icon-bar"></span>
		</button>

<!--    <div class="navbar-brand">
           <div class="media pull-right">
         	<img src="./trans_white_02.png" class="img-responsive" >
           </div> 
          </div> -->
	</div>
	<div class="navbar-collapse collapse">
		<div class="row" style="z-index:1000;background-color: #f3be22; line-height: 2; font-weight: 600;">
			<div class="col-md-1"></div>
			<div class="col-md-9 col-sm-12 col-xs-6" style="background-color: #f3be22; line-height: 2; font-weight: 600;">
				<div class="col-md-1 col-sm-1 col-xs-12 nav_author"  z-index:2000;">
					<span style="z-index:2000;">
					<!-- a href="javascript:void(0)" onclick="showAlpha2();">Authors </a>
					 -->
						<a href="#" class="dropdown-toggle"data-toggle="dropdown" style="padding-left: 0px; z-index:2000";>Authors</a>
						<ul class="dropdown-menu" style="text-align:left; z-index:2000;">
							<?php  echo $link_A;?>
						</ul>
					</span>
				</div>
				<div class="col-md-1 col-sm-1 col-xs-12 ">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" style="padding-left: 20px">Categories</a>
						<ul class="dropdown-menu" style="text-align:left">
							<?php  echo $catMenu;?>
						</ul>
				</div>
				<div class="col-md-1 col-sm-1 col-xs-12 nav_events">
						<a href="#" class="dropdown-toggle"data-toggle="dropdown" style="padding-left: 0px">Events</a>
						<ul class="dropdown-menu" style="text-align:left">
							<?php  echo $eventMenu;?>
						</ul>
				</div>
				<div class="col-md-3 col-sm-2 col-xs-12 nav_about" >
					<a href="javascript:void(0)"  onclick="aboutModal();">What is this site about</a>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12 nav_latest" >
						<a href="#LATEST PUBLICATIONS">Latest Publications</a>
				</div> 
				<div class="col-md-1 col-xs-12 nav_more">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" style="padding-left: 0px">More</a>
					<ul class="dropdown-menu">
						<li class="dropdown">
							<a href="javascript:void(0)"  onclick="triggerModal();">Welcome</a> 
							<a href="javascript:void(0)" onclick="showContact();">Contact Us</a>
							<a href ="./?published=1">Published Books</a>
							<a href ="./?top=1">Top Searches</a>
							<a href ="./?future=1">Coming Soon</a>
							<a href ="./?editorschoice=1">Editors Choice</a>
							<a href ="./?favourites=1">Favourites</a>
							
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
<!--  End Navbar -->
		 
<?php 
if($book->publisherurl	!=	"")	{ 
	$publisherLink	=	'<a  href="'.$book->publisherurl.'" target="_blank"  rel="nofollow">'.$book->publisher."</a>"; 
	}
elseif($book->publisher =='' ) { 
	$publisherLink	=	'<small>(unkown publisher) </small>'; 		
	}
else {
	$publisherLink = $book->publisher;
}
	
if(!$book->vendorurl	==	"")	{ 
	$vendorLink	=	"<a href=".$book->vendorurl.' target="_blank" rel="nofollow">'.$book->vendor."</a>"; 
	}
else { 
	$vendorLink	=	$book->vendor;
	}

$categoryLink = 'Category: <a href="../../?category='.$book->categoryid.'">'.$book->genre.'</a>';

$publishDate 		= date('d M Y',strtotime($book->published));

$image			 	= formatImage($book);

$bookAuthors = '';

$sep				=	'';		 // append to previous name 
foreach($book->authors as $authorID=>$authorName)	{
	$bookAuthors .= $sep.'<a href=./?author='.$authorID.'>'.rtrim($authorName).'</a> ';
	$sep =	'<br> ';
}
if(!$book->linkurl == '') $linkText = '<a href="'.$book->linkurl.'">'.$book->linktext. '</a>';

if($_SESSION['accessLevel']	== '10')	{
	$editLink = '<div class="col-md-2 info_links"><a href="publish/&edit?id='.$book->id.'">
							Edit
						</a></div>';
}
	$cover	=	"";
	if($book->hardback	== true )	{
		$cover	=	"<i><b>Hardback</b></i> ";
	}
	if($book->paperback	== true )	{
		$cover	.=	"<i><b>Paperback</b></i> ";
	}
	if($book->ebook	== true )	{
		$cover	.=	"<i><b>e-Book</b></i> ";
	}
	if($book->audio	== true )	{
		$cover	.=	"<i><b>Audio</b></i> ";
	}
	if($cover !=	"")	{
		$cover	=	"Available as ".$cover;
	}
	if(($book->ebook ==true) || ($book->audio == true) ) {
		if(!$book->size == '')	$bookSize = ' Size (electronic form of this book): '.(float)$book->size.'Kb'; 
	} 
	$pages	=	"";
	if($book->pages > 1)	{
		$pages	=	$book->pages." pages";
	}
	if(!$book->language == '') {
		$language = 'Language: '.$book->language;
	}
//var_dump($book);

?>
<div style="position:relative">
<div class="row" style=" background-color:black;padding:10px">
	<div class="col-md-1"></div>
		<div class="col-md-11">
	  	<font color="white"> 
				<div class="row" style="padding-top:20px;"><!-- Book display  -->
					<div class="row">
						<div class="col-md-3" style="padding-left:27px;padding-right:20px;" >
							<div class="col-md-12">			<!-- isbn's etc -->
								<div class="row "  style="padding:10px;margin:0px;" >		<!-- ISBN / 13 / ASIN -->
									<?php if($book->isbn13) { ?>
										<div class="col-md-12" STYLE="padding-left:0px;">
											<span >
														<!-- Facebook follow  -->
	<!-- a href="https://www.facebook.com/profile.php?id=100008804209153" target="_blank" style="padding-right: 0px; "> -->
	<a href="https://www.facebook.com/Irishinterest" target="_blank" style="padding-right: 0px; " rel="nofollow"> 
	
			<img src="../../facebook_button.png" class="image-responsive" style=" height:25px; margin-left:70px; border-radius:2px;" >
	</a>
<!-- Google Plus -->	
<a href="https://plus.google.com/101688982005763255838" target="_blank" rel="nofollow">
		<img src="../../google-plus-4-512.png" class="image-responsive"  style=" height:25px;margin-left:10px; border-radius:2px;">
</a>

<!-- Youtube -->	
<a href="https://www.youtube.com/channel/UCBVh-eIxXZEfh_BK9r8wwdQ" target="_blank" rel="nofollow">
		<img src="../../650d26_530a903910cd416ba6db299f004d1d2d.png" class="image-responsive"  style="height:25px;margin-left:10px; border-radius:2px;">
</a>
											
											</span> 
										</div>
										<?php 
										}  
										?>
								</div>
							</div>
						</div>
						<div class="col-md-6">
						<?php 
						if($signedin == true) { 
						/* 
						 * setup Favourite and Unfavourite buttons with one hidden according to book's status 
						 */
							$favBtn = 
								'<input class="btn" 
								style="background:none; white-space: normal; color:rgba(140,106,6,0.9); font-weight:900;';
							if($book->isFavourite == true) {
									$favBtn .= 'display:none';
							}			// close quote around style-
							$favBtn .=	'" 
								type="button" 
								onclick="fav('.$book->id.', '.$_SESSION['id'].'); " 
								id="favBtn"'; 
							$favBtn .= 'value="Add to Favourites">';
							
							$unfavBtn = 
								'<input class="btn" 
								style="background:none; white-space: normal; color:rgba(140,106,6,0.9); font-weight:900;';
							if($book->isFavourite == false) {
									$unfavBtn .= 'display:none';
							}			// close quote around style-
							$unfavBtn .=	'" 
								type="button" 
								onclick="unfav('.$book->id.', '.$_SESSION['id'].'); " 
								id="unfavBtn"'; 
							$unfavBtn .= 'value="Remove Favourite">';
?>
							<span class="pull-right">
								<div class="col-md-9 info_links" style="border:3px solid rgba(140,106,6,0.9);border-radius:25px;">
							<?php echo $favBtn.$unfavBtn; ?>
								<!-- input class="btn" 
									style="background:none;
											white-space: normal;
											color:rgba(140,106,6,0.9);
											font-weight:900;
											display:block;" 
									type="button" 
									id="favBtn" 
									<?php /*if(!$book->isFavourite == true) { ?>
									onclick="fav(<?php echo $book->id;?>, <?php echo  $_SESSION['id'];?>);" 
									value="Add to Favourites">
									<?php } 
									else { ?>
									onclick="unfav(<?php echo $book->id;?>, <?php echo  $_SESSION['id'];?>);"
												value="Remove Favourite"> 
										
									<?php } */?> -->
								</div>
							</span>
							<?php } ?>
						<font size="6"> 			<!--  title	-->
								<?php echo $book->title.'<br>';?>
							</font>
							<span class="info_links"><?php echo $bookAuthors; ?></span>
							<hr>
						</div>
					</div>
					<div class="row">	
						<div class="col-md-3" style="height:400px; padding-left:40px;"><!--  image -->
							<img class=" ii_image image-reponsive" 
								style="border:5px  solid grey; border-radius:1px;" 
								src="../../<?php echo $image;?>">
						</div>
						 <div class="col-md-9">
						  <div class="container col-md-9 info_links"> 	<!-- Info -->
							<div class="col-md-11">			<!-- authors,published, cover, pages -->
								<div class="row info_links">Publisher: 
									<font size="4" class="info_links" style="line-height:2;">
										<?php echo $publisherLink; ?>
									</font> 
									 ~  	
									<?php echo $publishDate?>
								</div>
								<div class="row info_links" ><p class="info_links" style="line-height:2;">
									<?php echo  $pages.' ~ '.$cover.'<br>';
									if(!$bookSize =='') echo $bookSize.'<br>'; 
									echo $categoryLink.'<br>';
									if(!$language =='') echo $language.'<br>';
									if(!$vendorLink =='') echo 'On sale at: '. $vendorLink.'<br>';
									if(!$linkText == '') echo $linkText.'<br>';?>
									</p></div>
							</div>
							</div>
							<div class="col-md-2"></div>
							<div class="col-md-8" style="padding:20px;padding-left:1px;background-color:#4d4d4d;border-radius:10px;">
								<div class="col-md-12" style="overflow-y:auto;height:200px;  background-color:#4d4d4d;border-radius:10px;">
									<p><?php echo $book->synopsis;?></p>
									</div>
							</div>
							<div class="col-md-3" style="position:absolute; bottom:150px; right:70px; ">
								<div class="row "  style="border:1px solid #f3be22; border-radius:5px;padding:10px;margin:0px;" >		<!-- ISBN / 13 / ASIN -->
									<?php if($book->isbn13) { ?>
										<div class="col-md-5" STYLE="padding-left:0px;">
											<span class="pull-right">ISBN-13:</span> 
										</div><div class="col-md-7">
											<?php echo $book->isbn13;?>
										</div>
										<?php 
										}  
										if($book->isbn) { ?>
										<div class="col-md-5">
											<span class="pull-right">ISBN:</span>
										</div><div class="col-md-7">
											<?php echo $book->isbn;?>
										</div>
										<?php } 
										if($book->asin) { ?>
										<div class="col-md-5">
											<span class="pull-right">ASIN:</span>
										</div><div class="col-md-7">
											<?php echo $book->asin;?>
										</div>
										<?php } ?>
								</div>
								</div>
							
						</div>
					</div>
								<div class="col-md-12" ><p>
									<div class="col-md-3"><i><h3>Tags:</h3>
										<b><?php echo $book->area; ?></b></i>
									</div>
								</p></div>
		 </font>
		</div>
		 </div>
		 </div>
	</div>
<?php 	var_dump($book); ?>
  	<div class="twitter_feed" >
				
					<div class="container-pad" id="property-listings" 
								style="background-color: #f3be22; 
								border: 0px solid; 
								border-radius: 25px;
								padding:11px;
								margin:0px";
								>
						
						 	<a class="twitter-timeline" 
  								href="https://twitter.com/Booksirish" data-widget-id="593740594373079041"  rel="nofollow">
		 						Tweets by @Booksirish
						 	</a>
		  			</div>
		 </div> 


<?php
if($_SESSION['contactReceived']	==	true) {
	echo '  <div class="row "  id="messageBox">
			  <div class="col-md-3 col-sm-1"></div>
			   <div class="col-md-5 col-xs-12 col-sm-11 voffset7">
    			<div class="alert dismiss" style="border-radius:25px; margin-left:75px;">
    				<button type="button" class="close input-group-button" href="javascript:void(0)" onclick="dismissMessageBox();" style="padding-left:0px" >
  			   			<span class="glyphicon glyphicon-remove  orange " style="text-shadow: black 1px 0px 0px; " title="Close">
  			   			</span>
    				</button>
    				<p class="text-center">'.$_SESSION['contactReceivedMessage'].'</p>
   				</div>
   			   </div>
   			  </div>
   			 </div>';
	$_SESSION['contactReceived']	=	"";
	$_SESSION['contactReceivedMessage']		=	"";
}
elseif($_SESSION['ok']	===	false)	{
	foreach($_SESSION['err']['field'] as $idx => $field)	{
		switch ($field)	{
			case 'contactName':
				$contactNameErr	=	"has-error";
				$contactNameMsg	=	$_SESSION['err']['msg'][$idx];
				break;
			case 'contactemail':
				$contactemailErr	=	"has-error";
				$contactemailMsg	=	$_SESSION['err']['msg'][$idx];
				break;
			case 'contactMessage':
				$contactMessageErr	=	"has-error";
				$contactMessageMsg	=	$_SESSION['err']['msg'][$idx];
				break;
		}
	}
}


$classHidden	=	' class="hidden contact" ';
if($_SESSION['ok'] === 	false) {
	$classHidden	=	"";
}
?>

<div id="contact"  <?php echo $classHidden;?>>
	<form role="form" class="form-horizontal">
		<fieldset><!-- Contact  Form -->
			<div class="col-md-2"></div>
			<div class="col-md-12 voffset3">
				<div class="alert dismiss well well-sm" style="border-radius: 25px;">
					<button type="button" class="close input-group-button" href="javascript:void(0)" onclick="dismissContact();"
									style="padding-left: 0px">
						<span class="glyphicon glyphicon-remove  orange " style="text-shadow: black 1px 0px 0px;" title="Close"> </span>
					</button>

					<p class="text-center" style="padding-bottom: 20px">Leave a message here, or mail us at administrator@IrishInterest.ie</p>
					<!-- EMail -->
					<div class="col-md-6">
						<div class="form-group  
							<?php echo $contactNameErr ; ?>">
							<label class="col-md-2 control-label control-label.has-error" for="contactName">From</label>
							<div class="col-md-9">
								<div class="controls">
									<input type="text" id="contactName" name="contactName" placeholder="your name" 
											class="form-control input-md" 
											required 
											size="40"
											value="<?php echo $_SESSION['contactName'] ; ?>"> 
									<span class="help-inline"><?php echo $contactNameMsg; ?></span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group  <?php echo $contactemailErr ; ?>">
							<label class="col-md-3 control-label control-label.has-error" for="contactemail">E-mail</label>
							<div class="col-md-9">
								<div class="controls">
									<input type="email" id="contactemail" name="contactemail" placeholder="your e-mail address" 
											class="form-control input-md" 	
											required 
												size="40"
											value="<?php echo $_SESSION['contactemail'] ; ?>"> 
													<span class="help-inline"><?php echo $contactemailMsg; ?></span>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group  <?php echo $contactMessageErr ; ?>">
						<div class="col-md-12">
							<div class="controls">
								<textarea class="form-control" rows="3" name="contactMessage" placeholder="your message" required>
									<?php echo $_SESSION['contactMessage'];?>
								</textarea> 
								<span class="help-inline"><?php echo $contactMessageMsg; ?></span>
							</div>
						</div>
					</div>
					<!-- Submit button  -->
					<div class="form-group">
						<div class="col-md-4">Irish Interest
							<table style="font-size: 10px; color: #526629;">
								<tbody>
									<tr>
										<td>Suite 34b, The book center, Camden Street,</td>
									</tr>
									<tr>
										<td>Dublin 2, <b>Ireland.</b></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="col-md-4 text-center">
							<div class="controls">
								<button type="submit" id="contact" name="contact" value="contact" 
											class="btn btn-md btn-primary btn-primary">Submit
								</button>
							</div>
						</div>
						<div class="col-md-4 pull-right">	
							<table class="pull-right" style="font-size: 10px; color: #526629;">
								<tbody>
									<tr>
										<td style="padding-top: 17px" class="pull-right">+ 353 1 254 8849</td>
									</tr>
									<tr>
										<td class="pull-right">administrator@irishinterest.ie</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>

			</div>
		</fieldset>
	</form>
</div>
<div class="modal  fade bs-example-modal-sm" id="event_modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span
						aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h1 class="modal-title">
					<img src="../../IRISH_INTEREST_text.png" class="img-responsive" style="height:30px;display:inline; background-color:#FFFFFF">
					<img src="../../ii_circle.png" class="img-responsive pull-right" style="height:70px;display:inline; padding-right:50px; background-color:#FFFFFF"></h2>
				</h1>
			</div>
			<div class="modal-body">
				<table id="eventTable">
					<tr>
						<td class="info_links" style="width: 600px"></td>
						<td></td>
					</tr>
				</table>
				<table id="eventDetailTable" style="word-wrap:break-word; table-layout: fixed; width: 100%">
					<tr>
						<td></td>
						<td></td>
					</tr>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div class="modal  fade bs-example-modal-sm" id="authors_modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span
						aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h1 class="modal-title">
				<img src="../../IRISH_INTEREST_text.png" class="img-responsive" style="height:30px;display:inline; background-color:#FFFFFF">
				<img src="../../ii_circle.png" class="img-responsive pull-right" style="height:70px;display:inline; padding-right:50px; background-color:#FFFFFF"></h2>
				</h1>
			</div>
			<div class="modal-body">
				<table id="authorTable">
					<tr>
						<td style="width: 600px"></td>
						<td></td>
					</tr>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<?php
function formatAuthors()	{
/*
	return	'Show <a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"all">all Authors</a><br> or Filter by:
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"a">a</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"b">b</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"c">c</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"d">d</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"e">e</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"f">f</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"g">g</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"h">h</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"i">i</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"j">j</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"k">k</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"l">l</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"m">m</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"n">n</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"o">o</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"p">p</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"q">q</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"r">r</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"s">s</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"t">t</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"u">u</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"v">v</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"w">w</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"x">x</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"y">y</a>
				<a  href="#authors_modal" data-toggle="modal"  	data-link-a		=	"z">z</a>
				';
	
				'<li style="padding:0px"><a href=?category='.$id.' style="padding-top:0px; padding-bottom:0px;">'.$category.'</a></li>';
				*/
		$alph = '<li style="padding:0px; font-size:14px; z-index:1000">
						<a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"all">Show all Authors</a><span style="margin-left:20px;color:grey"> or filter by:</span><br>
						</li>
				<li style="padding:0px; z-index:1000;"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"a">A</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"b">B</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"c">C</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"d">D</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"e">E</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"f">F</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"g">G</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"h">H</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"i">I</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"j">J</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"k">K</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"l">L</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"m">M</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"n">N</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;"  data-toggle="modal"  	data-link-a		=	"o�">O</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;"  data-toggle="modal"  	data-link-a		=	"p">P</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"q">Q</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"r">R</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"s">S</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"t">T</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"u">U</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"v">V</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"w">W</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"x">X</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px;" data-toggle="modal"  	data-link-a		=	"y">Y</a></li>
				<li style="padding:0px; z-index:1000"><a  href="#authors_modal" style="padding-top:0px; padding-bottom:0px; z-index:1000" data-toggle="modal"  	data-link-a		=	"z">Z</a></li>';
				
		return $alph;	
}

function formatImage($book)	{
	if($book->image != "")	{
		$image	=	"upload/".$book->image;
	}
	else {
		$image	=	"placeholder.jpg";
	}
	return $image;
}
?>
<script>
function unfav(book, user) {
	
	var request = $.ajax({
		url: "../../book/&Book_unfav="+book,
		data: {book: book},
		data: {user: user},
		type: "POST",			
		dataType: "html"
	});

	request.done(function(msg) {
		loc ="../../Book_unfav="+book;
		console.log(msg);
		//$("#mybox").html(msg);
/*		    document.getElementById('mybox').src = loc; */
		document.getElementById('favBtn').style.display = 'block';
		document.getElementById('unfavBtn').style.display = 'none';
/*		commitBtn.disabled = false;
		commitBtn.style.color = 'red';		
		console.log(file1);			*/
	});

	request.fail(function(jqXHR, textStatus) {
		alert( "Request failed: " + textStatus );
	});
}
	
	function fav(book, user) {
		
		var request = $.ajax({
		//	url: "../Nielsen_feed_db_update.php",
			url: "../../book/&Book_fav="+book,
			data: {book: book},
			data: {user: user},
			type: "POST",			
			dataType: "html"
		});

		request.done(function(msg) {
			console.log('Favourite Added ');
			//loc ="../Nielsen_feed_db_update.php?file=" + file;
			loc ="../../Book_fav="+book;
			console.log(msg);
			//$("#mybox").html(msg);
		document.getElementById('favBtn').style.display = 'none';
		document.getElementById('unfavBtn').style.display = 'block';
/*		    document.getElementById('mybox').src = loc;
			document.getElementById(file1).innerHTML += ' <span class="glyphicon glyphicon-star" aria-hidden="true"></span> ';
			commitBtn.disabled = false;
			commitBtn.style.color = 'red';		
			console.log(file1);			*/
		});

		request.fail(function(jqXHR, textStatus) {
			alert( "Request failed: " + textStatus );
		});
	}
</script>				

<script type="text/javascript"> /* contact popover */ 
$(function ()  
{ $("#contact").popover({title: 'Irish Interest', content: ""});  
});  
</script>
<script type="text/javascript"> /* document ready - carousel */

$(document).ready(function () {
	$('.carousel').carousel({
    interval: 6000
});

$('.carousel').carousel('cycle');
});  
</script>
<script type="text/javascript"> /* Ken Burns */
/**
 * author: Thierry Koblentz
 * Copyright 2011 - css-101.org 
 * http://www.css-101.org/articles/ken-burns_effect/css-transition.php
 */
!function(){function e()
{m==a&&(m=0),s[m].className="fx",0===m&&(s[a-2].className=""),1===m&&(s[a-1].className=""),m>1&&(s[m-2].className=""),m++}document.getElementById("slideshow").getElementsByTagName("img")[0].className="fx",window.setInterval(e,4e3);var s=document.getElementById("slideshow").getElementsByTagName("img"),a=s.length,m=1}();
</script>
<script type="text/javascript"> /* fns Misc modals - trigger/large, legal, privacy & about */
function triggerModal() {
 
   /* document.getElementById('the_id').value = avalue; */
 
    $('#largeModal').modal();
 
}
function legalModal() {
    $('#legalModal').modal();
}
function privacyModal() {
    $('#legalModal').modal();
}
function aboutModal() {
    $('#aboutModal').modal();
}
</script>
<script type="text/javascript"> /* changeContent, book_modal.onshow */
function changeContent(id, row, cell, content) {
   var x = document.getElementById(id).rows[row].cells;
    x[cell].innerHTML = content;
/*        */
}

$('#book_modal').on('show.bs.modal', function(e) {
    var bookImg 				= $(e.relatedTarget).data('book-image');
    var bookTitle 			= $(e.relatedTarget).data('book-title');
    var bookAuthor 			= $(e.relatedTarget).data('book-author');
    var bookDate 			= $(e.relatedTarget).data('book-date');
    var bookSynopsis		= $(e.relatedTarget).data('book-synopsis');
    var bookLinkurl			= $(e.relatedTarget).data('book-linkurl');
    var bookLinktext		= $(e.relatedTarget).data('book-linktext');
/*
*/
    var bookCover	 		= $(e.relatedTarget).data('book-cover');
    var bookPages	 		= $(e.relatedTarget).data('book-pages');
    var bookLanguage 	= $(e.relatedTarget).data('book-language');
    var bookIsbn10 			= $(e.relatedTarget).data('book-isbn10');
    var bookIsbn13 			= $(e.relatedTarget).data('book-isbn13');	
    var bookPublisher 		= $(e.relatedTarget).data('book-publisher');
	var bookPublisherUrl 	= $(e.relatedTarget).data('book-publisherurl');
    var bookVendor 		= $(e.relatedTarget).data('book-vendor');
    var bookVendorUrl		= $(e.relatedTarget).data('book-vendorurl');
    var bookAsin				= $(e.relatedTarget).data('book-asin');
    var bookSize				= $(e.relatedTarget).data('book-size');
    var bookID				= $(e.relatedTarget).data('book-id');
    var signedin 				= $(e.relatedTarget).data('signed-in');
    var favourite 				= $(e.relatedTarget).data('favourite');
    var narrative 				= $(e.relatedTarget).data('narrative');

    if(signedin == true) {
        if(favourite == true) {
            var favHtml = '<center><small class="info_links" ><a href="?unfav='+bookID+'">Remove favourite</a></small></center>';
        }
        else {
    		var  favHtml = '<center><small class="info_links" ><a href="?fav='+bookID+'">Add to favourites</a></small></center>';
        }
	   changeContent('fav',0,1,favHtml);
    }
    if(narrative !== '')
        narrativeHtml = '<p>' + narrative + '</p>';
    	var  imgHtml = '<img class="img-responsive" style="background-color:#ffffff; max-width:300px; max-height:250px; float:right;  padding:5px; margin:10px" name="bookImage1" id="bookImage1" ';
  	imgHtml1	= imgHtml + ' src="' + bookImg + '">';	
	changeContent('myTable',0,0,narrative + imgHtml1 + '<h3>' + bookTitle + '</h3><p >by <b class="info_links" >'  + bookAuthor + '</b></p><p>' + bookDate +
			'</p><p>'  + bookSynopsis + '</p> <a href=' + bookLinkurl + ' target="_blank" rel="nofollow">' + bookLinktext + 
			'</a></p>'); 

	var div0	='<div class="table-responsive">';
	div0		= div0 + '<table class="table  table-bordered">';
	div0		= div0 + '<tr><td>Publisher </td><td>'+ bookPublisher + '</td></tr>';
	div0		= div0 + '<tr><td>Language </td><td>'+ bookLanguage +'</td></tr>';
	div0		= div0 + '<tr><td>ISBN10 </td><td> ' + bookIsbn10 + '</td></tr>';
	div0		= div0 + '<tr><td>ISBN13</td><td> ' + bookIsbn13 + '</td></tr>';
	div0		= div0 + '<tr><td>ASIN</td><td> ' + bookAsin + '</td></tr>';

	div0		= div0 + '<tr><td>Publisher\'s web site</td><td class="info_links" ><a href=" ' + bookPublisherUrl 	+ '" target="_blank" rel="nofollow">' +bookPublisher + '</a></td></tr>';
	div0		= div0 + '<tr><td>Link to bookseller</td><td class="info_links" >	  <a href="'  + bookVendorUrl 		+ '" target="_blank" rel="nofollow">' +bookVendor 	+ '</a></td></tr>';

	
	div0		= div0 + '</table></div>';
	changeContent('myTable',4,0,div0); 
	changeContent('myTable',5,0,'<p>' + bookPages+'</p>');  
	changeContent('myTable',6,0,'<p>' + bookCover+'</p>');
	changeContent('myTable',7,0,'<p>' + bookSize +'</p>');
	  
//	changeContent('myTable',7,0,'<p><a href="' + bookPublisherUrl + '" target="_blank">' + bookPublisher + '</a></p>'); 
//	changeContent('myTable',8,0,'<p><a href="' + bookVendorUrl 	  + '" target="_blank">' + bookVendor  	 + '</a></p>');  

});
</script>
<script type="text/javascript"> /* infoModal */
function triggerInfoModal() {
 
   /* document.getElementById('the_id').value = avalue;*/
 
    $('#infoModal').modal();
 
}
</script> 
<script type="text/javascript"> /* show contact */
function showContact() {
  $("#contact").hide().removeClass('hidden');
  $("#contact").fadeIn();
}
</script> 
<script type="text/javascript"> /* Dismiss Contact*/
function dismissContact() {
  $("#contact").hide().addClass('hidden');
}
</script> 
<script type="text/javascript"> /* Dismiss Message Box */
function dismissMessageBox() {
  $("#messageBox").hide().addClass('hidden');
}
</script>
<script type="text/javascript"> /* showAlphabet.onShow, alphabetSearch */
$('#showAlphabet').on('show.bs.modal', function (event) {
	var btns = "";
	var letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	var letterArray = letters.split("");
	for(var i = 0; i < 26; i++){
		var letter = letterArray.shift(); 
		btns += '<button class="mybtns" onclick="alphabetSearch(\''+letter+'\');">'+letter+'</button>';
		}
		changeContent('letterTable',0,0,btns);  
		console.log("I'm nearly there");
		})

function alphabetSearch(let) {
	console.log("I'm Here");
	window.location="alphabet_search_results.php?letter="+let;
	return false;
}
</script>
<script type="text/javascript"> /* showAlphabet, showAplha2, dismissAlpha2, selectAlpha2  */
function showAlphabet() {

    $('#showAlphabet').modal();
 
}
function showAlpha2() {
	  $("#alpha2").hide().removeClass('hidden');
	  $("#alpha2").fadeIn();
	}
function dismissAlpha2() {
	  $("#alpha2").hide().addClass('hidden');
	}
function selectAlpha2(ltr) {
		dismissAlpha2();
	    $('#authors_modal').modal();
	}
	$('#authors_modal').on('show.bs.modal', function(e) {
		
	    var letters		= $(e.relatedTarget).data('link-a');
	    var authors 	= JSON.parse(<?php echo json_encode($authors); ?>);

	    var table = document.getElementById("authorTable");
	    var rowCount = table.rows.length;
	    //console.log(rowCount);
	    for (var x=rowCount-1; x>0; x--) {
		  //  console.log('Deleting Row: ' + x);
	       table.deleteRow(x);
	    }
	    rowCount = 1;
	    selection = '<h4>Author names beginning with the letter ' + letters.substr(0,1).toUpperCase() +'</h4>';
		if(letters == 'all') {
			selection = '<h4>all Authors</h4>';
		}
		    
	    changeContent('authorTable',0,0,selection); 
	    var j = 1;
	    for (i = 0; i < authors.length; i++) {
		 //   console.log(authors[i].indexOf(letter) );
		 	var author = '<a href="../../?author=' + authors[i][1] + '">' + authors[i][0] + '</a>';
		 	if(letters == 'all')	{
			    row = authorTable.insertRow(j);
			    row.insertCell(0);
		   	 	changeContent('authorTable',j,0,author);
			    j++;
				
			 	}
		 	else {
			 //	console.log(letters + '  ' +authors[i][0]); working on � 
			  for(k=0; k < letters.length; k++) {
				letter = letters.substring(k,k+1);
				//	  console.log(k + ' ' +escape(letter));
				if(letter !== '') {
				//	authorfirstchar = authors[i][0].substring(0,1);
				//	console.log(k + ' ' +escape(letter) + ' ' + escape(authorfirstchar) );
				//	if( ( (authorfirstchar == letter)  || (authors[i][0].indexOf(letter.toUpperCase()) == 0) ) || ( (escape(authorfirstchar) == escape(letter) ) ) ) {
				 	if ( (authors[i][0].indexOf(letter) ==  0 )  || (authors[i][0].indexOf(letter.toUpperCase()) == 0) )  {
				// 
				//	    console.log(i);
					    row = authorTable.insertRow(j);
				    	row.insertCell(0);
		   		 		changeContent('authorTable',j,0,author);
			    		j++;
				 	}
		    	}
			  }
		 	} 
	    }
		});
</script> 
<script type="text/javascript"> /* showEvent, dismissEvent, selectEvent, eventModal.onshow, */
	function showEvent() {
   			$('#event_modal').modal();
		}

	function dismissEvent() {
		  $("#event_modal").hide().addClass('hidden');
		}
	function selectEvent(id) {
			dismissEvent();
		    $('#event_modal').modal();
		}
	$('#event_modal').on('show.bs.modal', function(e) {
		var title 			= $(e.relatedTarget).data('event-title');
		var description 	= $(e.relatedTarget).data('event-description');
		var location 		= $(e.relatedTarget).data('event-location');
		var from 			= $(e.relatedTarget).data('event-from');
		var to 				= $(e.relatedTarget).data('event-to');
		var link 			= $(e.relatedTarget).data('event-link');
		var image			= $(e.relatedTarget).data('event-image');
		var author			= $(e.relatedTarget).data('event-author');
		var book			= $(e.relatedTarget).data('event-book');
		console.log('Book Title = '+ book)
   		// the Image 	
   	   	var  imgHtml = '<a href ="' + link +'" target="_blank" rel="nofollow"> <img class="img-responsive" style="min-height:100px; max-height:200px; min-width:100px; max-width:200px; float: right;  padding:5px; margin:10px; background-color:#FFFFFF " name="image" id="image" ';
   	  	imgHtml1	= imgHtml + ' src="' + image + '"></a>';	
		var style1 =  'style="padding-right:10px; padding-bottom:10px;width:15%;vertical-align:top;"';
		var style2 =  'style="padding-right:10px; padding-bottom:10px;width:85%"';
		var table = document.getElementById("eventTable");
	    var rowCount = table.rows.length;
		// clear the table
	    for (var x=rowCount-1; x>0; x--) {
	       table.deleteRow(x);
	    }
		var detailTable = document.getElementById("eventDetailTable");
	    var rowCount = detailTable.rows.length;
		// clear the table
	    for (var x=rowCount-1; x>-1; x--) {
	       detailTable.deleteRow(x);
	       console.log(x);
	    }
	    rowCount = 1;
	    selection = '<a href="' +link+ '" target="_blank" rel="nofollow"><h4>'+ title + '</h4></a>';
		    
	    changeContent('eventTable',0,0,author + ' ' + book + selection +  description); 
   	 	changeContent('eventTable',0,1,imgHtml1);
		    
	    row = eventTable.insertRow(1);
	    row.insertCell(0);
//   	 	changeContent('eventTable',1,0,description);

   	 	row = eventDetailTable.insertRow(0);
   	 	
	    newcell = row.insertCell(0);
	    newcell.className = "eventdetailcell";
	    changeContent('eventDetailTable',0,0,'<table style="table-layout:fixed; word-wrap:break-word; width:100%;">' +
	    	    '<tr><td '+ style1 +'>Where:</td><td '+ style2 +'> ' + location +'</td></tr>' +
	    	    '<tr><td '+ style1 +'>On:</td><td '+ style2 +'> ' + from +'</td></tr>' +
	    	    '<tr><td '+ style1 +'>To:</td><td '+ style2 + '> ' + to + '</td></tr>' +
	    	    '<tr><td '+ style1 +'>Link:</td><td '+ style2 +'> <a href="' + link + '" target="_blank" rel="nofollow">' + link +'</a></td></tr></table>');

//	    row = eventTable.insertRow(3);
//	    row.insertCell(0);
//   	 	changeContent('eventTable',3,0,'<tr><td>On:</td><td> ' + from +'</td></tr>');

//   	 	row = eventTable.insertRow(4);
//	    row.insertCell(0);
 //  	 	changeContent('eventTable',4,0,to);

	   	  	
		// the Link
	//		row = eventTable.insertRow(6);
	//	    row.insertCell(0);
	 //  		changeContent('eventTable',6,0,'<p><a href="' + link 	  + '" target="_blank">' + 'Web Page'  	 + '</a></p>');  
			
	}); 	
</script> 

		 <script>!function(d,s,id){	/* Twitter */
			 var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';
			 if(!d.getElementById(id)){	
				 js=d.createElement(s);
				 js.id=id;
				 js.src=p+"://platform.twitter.com/widgets.js";
				 fjs.parentNode.insertBefore(js,fjs);
				 }}
		 (document,"script","twitter-wjs");
		 </script> 

<script type="text/javascript"> /* position at searchresults */
	window.location="#searchresults";
</script>
</body>
</html>