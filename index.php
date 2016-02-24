#!/usr/bin/php
<?php
$VERSION="3.7";
$DEVELOPMENT_MODE=FALSE;
date_default_timezone_set("Europe/London");
require("../visitorlog.php");
$logger=new VisitorLogger("log/georgia.log");
if(!$DEVELOPMENT_MODE){
  $logger->log();
}
function d($k){
  global $display;
  return $display[$k];
}
$display['nav'] = file_get_contents("dirList");
?>
<html>
  <head>
    <title>Georgia - your lovely CATe pdf viewer.</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <link rel="stylesheet" href="./include/css/index.css">
    <link href="favicon.ico" rel="icon" type="image/x-icon" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="Georgia is a online pdf viewer which has all the notes/exercises from CATe, a system used by the Department of Computing, Imperial College.">
    <meta name="keywords" content="georgia imperial doc computing pdf viewer notes cate">
    <script>
//changePDF
function changePDF(path, elem){
  $("object").html("Your browser doesn't support viewing PDF. Change a browser or click <a href=\""+path+"\">here</a>.");
  $(".row li").css("background-color","");
  $(elem).parent().css("background-color","rgba(81, 203, 238, 0.7)");
  pureChangePDF(path);
  return false;
}
function getGviewerURL(servePdfUrl){
  return 'https://docs.google.com/viewer?embedded=true&url='+encodeURIComponent(servePdfUrl);
}
function getPdfUrl(pdfUrl){
  return $.post("getServePdfAuth.php", {file: pdfUrl});
}
//changePDF
function pureChangePDF(p){
  getPdfUrl(p).done(function(path){
    path=path.trim();
    path=getGviewerURL(path);
    var newObject = $('<iframe src="'+path+'" class="test" type="application/pdf"></iframe>');
    $(".download-button").parent().attr("href",path);
    $("#pdfviewer").html(newObject);
  <?php if(!$DEVELOPMENT_MODE){?>
    if(path.split("./pdf/")[1]==undefined){
      $.post("addAction.php",{action:path.split("./")[1]});
    }else{
      $.post("addAction.php",{action:path.split("./pdf/")[1]});
    }
  <?php }?>
  });
}
pureChangePDF('./pdf/keepCalmAndRevise.pdf'); 
    </script>
  </head>
  <body>
<div id="fb-root"></div>
<script>(function(d, s, id) {
var js, fjs = d.getElementsByTagName(s)[0];
if (d.getElementById(id)) return;
js = d.createElement(s); js.id = id;
js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3";
fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
    <header class="jumbotron">
      <div class="container-fluid">
        <h1>Georgia <?=$VERSION?>, <small>your lovely CATe pdf viewer.</small></h1>
        <h4 style="margin-left:10px">As usual, use it at your own risk. Please tell me if the notes are outdated. (cmy14@ic.ac.uk)</h4>
        <div class="nav"> </div>
        <h5 class="copyright"><a target="_blank" href="http://www.doc.ic.ac.uk/~cmy14">Jason Yu</a> &copy; 2015</h5>
        <div class="fb visible-lg"><div class="fb-page" data-href="https://www.facebook.com/GeorgiaYourViewer" data-width="280" data-height="130" data-hide-cover="true" data-show-facepile="true" data-show-posts="false"></div></div>
        <div class="hidden-lg"><div class="fb-page" data-href="https://www.facebook.com/GeorgiaYourViewer" data-width="500" data-height="130" data-hide-cover="true" data-show-facepile="true" data-show-posts="false"></div></div>
      </div>
    </header>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12 col-lg-3"> 
          <div class="scrollerSelectPDF">
            <div class="selectPDF">
            </div>
          </div>
        </div>
        <div class="col-md-12 col-lg-9"> 
          <a href="./pdf/keepCalmAndRevise.pdf" download>
            <img title="Download pdf" src="include/images/download.png" class="download-button">
          </a>
          <img title="Half fullscreen" src="include/images/halffullscreen.png" class="halffullscreen-button visible-lg">
          <img title="Fullscreen" src="include/images/fullscreen.png" class="fullscreen-button">
          <div id="pdfviewer">
            <object class="test" type="application/pdf" data="./pdf/keepCalmAndRevise.pdf"> 
            </object>
          </div>
        </div>
      </div>
    </div>
<script>
//populate the navbar
var nav = <?=d('nav');?>;
function populateNavbar(obj, ul){
  //var order = ["c1","c2","c3","c4","j1","j2","j3","j4","i2","i3","i4","v5","s5","a5","r5","y5","b5"];
  var order = ["c2"];
  $.each(order,(function(u, o){
    return function(k,v){
              if(o[v]==undefined){ return;}
              var li = $("<li>"+v+"</li>");
              u.append(li);
              if(o[v].length > 0){
                var ul = $("<ul></ul>");
                li.append(ul);
                populateNavbarDropdown(o[v], ul, v);
              }
            }})(ul, obj));
  addWhyGeorgiaLi(ul);
  addChangelogLi(ul);
}
function populateNavbarDropdown(obj, ul, cls){
  $.each(obj,(function(u){
    return function(k,v){
              if(k=="length") return;
              var li = $("<li><a href=\"#"+cls+k+"\" class=\"subject\">"+k+"</a></li>");
              u.append(li);
            }})(ul));
}
var navBarUl = $("<ul class=\"visible-lg dropdown\"></ul>");
populateNavbar(nav, navBarUl);
$(".nav").append(navBarUl);
//v2.2
var navBarUl = $("<ul class=\"hidden-lg slider\"></ul>");
populateNavbar(nav, navBarUl);
$(".nav").append(navBarUl);
//no stop youtube -> changeSelectPDF using JS
function changeSelectPDF(){
  if(this==window){
    q = window.location.hash;
  }else{
    q = this.hash;
  }
  if(q==undefined) return;
  cls=q.slice(1,3);
  subject=q.slice(3);
  if(cls=="" || subject=="") return;
  $.each(nav, function(k,v){
    if(cls==k){
      $.each(nav[cls],function(k,files){
        if(k=="length"){return;}
        if(subject==k){
          html="<ul><li class=\"nonClickable\">"+cls+": "+subject+"</li>";
          html+="<li class=\"nonClickable\">Notes</li>";
          $.each(files, function(file, fullpath){
            if(file=="exercise"){
              html+="<li class=\"nonClickable\">Exercise</li>";
              $.each(fullpath, function(exerciseName, fullpath){
                if(exerciseName=="length"){return;}
                html+="<li class=\"exName\">"+exerciseName+"</li>";

                $.each(fullpath, function(file, fullpath){
                  if(file=="length"){return;}
                  html+="<li>\
                    <a onclick=\"changePDF('"+fullpath+"',this)\">"+file.split(".pdf")[0]+"</a>\
                  </li>";
                })
              })
            }else{
              if(file=="length"){return;}
              html+="<li>\
                <a onclick=\"changePDF('"+fullpath+"',this)\">"+file.split(".pdf")[0]+"</a>\
              </li>";
            }
          });

          html+="</ul>";

          $(".selectPDF").html(html);
          var $target = $(".anchor");
          if($target.css("display")!="none"){
            sum=0;
            $(".nav>ul.slider>li>ul:visible").each(function(){
              $(this).slideUp("fast",function(){
                $(this).parent().css("background-color","");
              });
              sum+=parseInt($(this).css("height"));
            });
            $('html, body').stop().animate({
                'scrollTop': $target.offset().top-sum
            }, 900, 'swing');
          }
        }
      });
    }
  }); 
}
changeSelectPDF();
$(".nav>ul>li>ul>li>a.subject").click(changeSelectPDF)
$(".nav ul.dropdown>li>ul").click(function(){
  $(this).css("display","none");
  goHalfFullscreen();
});
$(".nav ul.dropdown>li>ul").hover(function(){
  $(this).removeAttr("style");
});
//slidebar effect
$(".nav ul.slider>li").click(function(){
  if($(this).data("opened")){
    $(this).children("ul").slideUp("fast",function(){
      $(this).parent().css("background-color","");
    });
    $(this).data("opened",false);
  }else{
    $(this).children("ul").slideDown();
    $(this).css("background-color","#DDD");
    $(this).data("opened",true);
  } 
});
//\v2.2
//Why Georgia?
function addWhyGeorgiaLi(navBarUl){
  whyGeorgiaLi=$("<li>Why Georgia?</li>");
  navBarUl.append(whyGeorgiaLi)
  ul=$("<ul></ul>");
  whyGeorgiaLi.append(ul);
  whygeorgiawhyLi=$("<li><a><image src=\"./include/images/stopbutton.png\" width=\"20\" style=\"margin-right:8px;display:none;\">\
    <image src=\"./include/images/playbutton.png\" width=\"20\" style=\"margin-right:8px;\">\
    Why Georgia why?</a><li>");
  georgiaonmymindLi=$("<li><a><image src=\"./include/images/stopbutton.png\" width=\"20\" style=\"margin-right:8px;display:none;\">\
        <image src=\"./include/images/playbutton.png\" width=\"20\" style=\"margin-right:8px;\">coz, Georgia on my mind.</a><li>");
  ul.append(whygeorgiawhyLi)
  ul.append(georgiaonmymindLi)
  whygeorgiawhyLi.click(togglePlayMusic("<img src=\"./include/images/musicnote.png\" width=\"40\"> Playing Why Georgia? by John Mayer","ybbDIz_1yqs"));
  georgiaonmymindLi.click(togglePlayMusic("<img src=\"./include/images/musicnote.png\" width=\"40\"> Playing Georgia on my mind by Michael Buble.","Qg33LxkbOtI"));
  function togglePlayMusic(s,v){
    return function(){
      $(this).find("img").toggle();
      if(!$(this).data("playing")){
        toast(s);
        $(this).data("youtube",playYoutube(v));
        $(this).data("playing", true)
      }else{
        $(this).data("youtube").remove();
        $(this).data("playing", false)
      }
    }
  }
}
//changelog
function addChangelogLi(navBarUl){
  changelogLi=$("<li>ChangeLog & References</li>");
  navBarUl.append(changelogLi)
  ul=$("<ul></ul>");
  changelogLi.append(ul)
  function addChangelog(s){
    addReference(s,"");
  }
  function addReference(s, url){
    if(url!=""){
      ul.append("<li><a href=\""+url+"\" target=\"_BLANK\">"+s+"</a><li>")
    }else{
      ul.append("<li><a>"+s+"</a><li>")
    }
  }
  ul.append("<li><a>ChangeLog:</a><li>")
  /*addChangelog("v1.1: NavBar Scroll bug fix. Credits to: Aboh Obiora (01 Apr, 2015)");  
  addChangelog("v1.1: Fullscreen is available now. Credits to: Amey Kusurkar (01 Apr, 2015)");  
  addChangelog("v2.0: Georgia now has all the notes from undergrads to postgrads. Credits to: Nic Prettejohn (02 Apr, 2015)");  
  addChangelog("v2.1: Wanna know why Georgia? Go to the \"Why Georgia?\" section and find out more. (Videos are embeded from YouTube.) (02 Apr, 2015)"); 
  addChangelog("v2.2: Improved mobile/tablet compatibility. As mobile/tablet cannot view pdf, the pdf will be downloaded instead. (02 Apr, 2015)");  
  addChangelog("v2.2: Now you could change to other subject when you are playing Georgia. (02 Apr, 2015)");  
  ul.append("<li><a onclick=\"pureChangePDF('./pdf/keepCalmAndRevise.pdf')\">v2.2: Keep Calm and Revise pdf is preloaded before a pdf is selected. Or click on me. (02 Apr, 2015)</a></li>"); 
  addChangelog("v2.3: Fullscreen stays on same page in pdf. (03 Apr, 2015)");  
  addChangelog("v2.3: Download button added. (03 Apr, 2015)");  
  addChangelog("v2.3: Half-fullscreen is implemented. (03 Apr, 2015)");  
  addChangelog("v2.3: Dropdown menu disappear after clicking. (03 Apr, 2015)");   */
  addChangelog("v3.0: Exercises are also available from Georgia. (04 Apr, 2015)");  
  addChangelog("v3.1: Exercise answers are also available from Georgia. (06 Apr, 2015)");  
  addChangelog("v3.1: Like Georgia's Facebook Page now. :) (06 Apr, 2015)");  
  addChangelog("v3.2: Exercises in order of CATe. (07 Apr, 2015)");  
  addChangelog("v3.2: Fixed half-fullscreen bug. (07 Apr, 2015)");  
  addChangelog("v3.3: Half-fullscreen improvement. (10 Apr, 2015)");  
  addChangelog("v3.3: NavBar font-size 20px->18px so it looks nice on 13inch MBA.(This change is totally for personal reasons.) (10 Apr, 2015)");  
  addChangelog("v3.4: Hitting spacebar will bring you to half-fullscreen mode. [thanks to: Pontus Liljeblad for proposing] (11 Apr, 2015)");  
  addChangelog("v3.4: Toast message appearing when toggling half-fullscreen mode. (11 Apr, 2015)");  
  addChangelog("v3.5: Update on PDFs due to some changes made on them. (17 Apr, 2015)");  
  addChangelog("v3.6: Added restriction for Georgia. (18 Feb, 2016)");  
  addChangelog("v3.7: Now Georgia is very compatible with other browsers. Thanks to Google Drive. (24 Feb, 2016)");  
  addChangelog("v3.71: Added mobile compatibility. (24 Feb, 2016)");  
  ul.append("<li><a>References:</a><li>")
  addReference("Youtube videos are embeded. Please click on the video to find out more","");
}
function playYoutube(v){
  youtube = $("<iframe width=\"210\" height=\"157\" src=\"https://www.youtube.com/embed/"+v+"?autoplay=1\"\
  frameborder=\"0\" allowfullscreen style=\"position:fixed; top:30; right:10;z-index:4\"></iframe>");
  $("body").append(youtube);
  return youtube;
}
function toast(s){
  toastDiv = $("<div></div>").css("display", "inline").css("position","fixed").css("bottom","50px").css("left","50%")
  toastDiv.css("padding","20 0").css("width","500px").css("text-align","center").css("margin-left","-250px").html(s);
  //styling
  toastDiv.css("background","rgba(255, 128, 128, 0.9)").css("font-size","18px").css("cursor","default").css("color", "rgb(200,40,40)");
  $("body").append(toastDiv.fadeIn("slow").delay(2000).fadeOut("slow"));
}

function getCurrentOffsetTop(elem){
  return elem.offset().top-$(window).scrollTop()
}
function getCurrentOffsetLeft(elem){
  return elem.offset().left-$(window).scrollLeft()
}

//fullscreen
$(".fullscreen-button").click(goFullscreen);
function goFullscreen(){
  if(!$("#pdfviewer iframe").data("isFullscreen")){
    $("#pdfviewer iframe").css("top",-getCurrentOffsetTop($("#pdfviewer iframe"))).css("left",-getCurrentOffsetLeft($("#pdfviewer iframe"))).css("height",window.innerHeight).css("width",window.innerWidth).data("isFullscreen",true);
    $("body").css("overflow","hidden");
    toast("Entering fullscreen mode.");
  }else{
    fullscreen = $("#pdfviewer iframe").removeAttr("style").data("isFullscreen", false);
    $("body").css("overflow","");
    toast("Quiting fullscreen mode.");
  }
}
//half fullscreen
function goHalfFullscreen(){
  if(!$("html").data("isHalfFullscreen")){
    toast("Entering half-fullscreen mode. Hit [SPACE] to quit.");
    //$(window).scrollTop(0);
    $("header").slideUp();
    $("html").data("isHalfFullscreen", true);

  }else{
    toast("Quiting half-fullscreen mode. Hit [SPACE] to enter.");
    $('html, body').animate({
        scrollTop: 0
    }, 500);
    $("header").slideDown();
    $("html").data("isHalfFullscreen", false)
  }
}
$(".halffullscreen-button").click(goHalfFullscreen);
$("body").keydown(function(e){
  if(e.keyCode==32){
    goHalfFullscreen()
  }
  if(e.keyCode==27){
    goFullscreen()
    $(window).scrollTop(0);
  }
});

//navbar overflow bug
$(".nav>ul>li").hover(function(){
  dropdown=$(this).children("ul");
  offset = dropdown.offset();
  if($(".container-fluid:last-child").width()<offset.left+dropdown.width()){
    dropdown.css("left","").css("right","").addClass("pullLeft");
  }
  else{
    dropdown.css("left","").css("right","").removeClass("pullLeft");
  }
});
</script>
  </body>
</html>
