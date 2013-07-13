<html>

<?php

echo "VisializIN <br />";
require_once("facebook.php");
$facebook = new Facebook(array(
  'appId'  => getenv('api_key'),
  'secret' => getenv('api_secret'),
));

$user = $facebook->getUser();

if ($user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
    $likes = $facebook->api('/me?fields=likes');
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
}

// Login or logout url will be needed depending on current user state.
if ($user) {
  $logoutUrl = $facebook->getLogoutUrl();
} else {
  $loginUrl = $facebook->getLoginUrl();
}

// This call will always work since we are fetching public data.
//$naitik = $facebook->api('/me');

?>

<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>VisualizIN</title>
     <script type="text/javascript" src="http://mbostock.github.com/d3/d3.js"></script>
    <style>
      body {
        font-family: 'Lucida Grande', Verdana, Arial, sans-serif;
      }
      h1 a {
        text-decoration: none;
        color: #3b5998;
      }
      h1 a:hover {
        text-decoration: underline;
      }
    </style>
  </head>
  <body>
    <h1>VisualizIN</h1>

    <?php if ($user): ?>
      <a href="<?php echo $logoutUrl; ?>">Logout</a>
    <?php else: ?>
      <div>
        Login using OAuth 2.0 handled by the PHP SDK:
        <a href="<?php echo $loginUrl; ?>" onclick>Login with Facebook</a>
      </div>
    <?php endif ?>

    <!--<h3>PHP Session</h3>
    <pre><?php print_r($_SESSION); ?></pre>
    -->


    <?php if ($user): ?>
      <h3>You</h3>
      <img src="https://graph.facebook.com/<?php echo $user; ?>/picture">

      <h3>Your User Object (/me)</h3>
      <pre><?php // print_r($likes); ?></pre>
    <?php else: ?>
      <strong><em>You are not Connected.</em></strong>
    <?php endif ?>


    <?php
    $like_query="select page_id,name,page_url from page where page_id in(select page_id from page_fan WHERE uid=me())";

    $response= $facebook ->api(array(
      'method' => 'fql.query',
      'query' => $like_query,));



// print_r($response);

$len=count($response);
$pages=array();
$name=array();
$page_url=array();

 foreach ($response as $page_detail) {
      $pages[]=$page_detail['page_id'];
      $name[]=$page_detail['name'];
      $page_url[]=$page_detail['page_url'];

  } 


  $rand_keys=array_rand($name,30);
  $rand_id=array();
 $random=array();
  foreach ($rand_keys as $key) {
    # code...
    $random[]=$response[$key]['name'];
    $rand_id[]=$response[$key]['page_id'];
  }
  
 ?>



<div id="viz"></div>
<script type="text/javascript">

  var jArray = <?php echo json_encode($random ); ?>;
   var pageid = <?php echo json_encode($rand_id ); ?>;

  
  pageid.length=10;
  jArray.length=10;
  for(var i=0;i<10;i++){
    console.log(jArray[i]);
   console.log(pageid[i]);
       
  }

  var user = <?php echo $user; ?>;

    i = 0;
    
var currentItem=0;
var currname="";

var sampleSVG = d3.select("#viz")
    .append("svg")
    .attr("width", 2000)
    .attr("height", 2000);    




function generate_circles(idlist,namelist, xcenter, ycenter, centertext){

 

    dataset=[]
    for(i=0; i<namelist.length; i++){
        dataset.push(Math.round(30 + Math.random()*50));
    }      
    currentcircles=[];
     for(var i=0;i<namelist.length;i++){
        dist=250 + Math.random()*200
        var xc=xcenter+dist*Math.cos(2*i*Math.PI/namelist.length) 
        var yc=ycenter+dist*Math.sin(2*i*Math.PI/namelist.length)



        t=sampleSVG.append("svg:line")
        .attr("x1", xcenter+100*Math.cos(2*i*Math.PI/namelist.length))
        .attr("y1", ycenter+100*Math.sin(2*i*Math.PI/namelist.length))
        .attr("x2", xcenter+(dist-dataset[i])*Math.cos(2*i*Math.PI/namelist.length))
        .attr("y2", ycenter+(dist-dataset[i])*Math.sin(2*i*Math.PI/namelist.length))
        .style("stroke", "rgb(6,120,155)");
        currentcircles.push(t);
        tempcircle = sampleSVG.append("circle")
        .attr("r", dataset[i])
        .attr("cx", xc)
        .attr("cy", yc)
        .attr("fill", "red")
        .style("opacity", 0.5)
        .attr("id",i)
        .on("mouseover", function(){
            d3.select(this).style("opacity", 1);
            d3.select(this).attr("r", d3.select(this).attr("r")*2);
        })
        .on("mouseout", function(){
            d3.select(this).style("opacity", 0.5);
            d3.select(this).attr("r", d3.select(this).attr("r")/2);
        }).on("click", function(){
            currentItem=d3.select(this).attr("id");
            currid=idlist[parseInt(currentItem)].toString();
            currname=namelist[parseInt(currentItem)].toString();
            console.log(currentItem);
            console.log(currname);
            xclicked=d3.select(this).attr("cx");
            yclicked=d3.select(this).attr("cy");
            rclicked=d3.select(this).attr("r");
            j=0;
            while(j<currentcircles.length){                
                d3.select(currentcircles[j][0][0]).style("opacity", 0.1)

                .on("mouseover", function(){})
                .on("mouseout", function(){})
                .on("click", function(){});

                j++;
            }



            tcenter=sampleSVG.append("circle")
            .attr("r", rclicked)
            .attr("cx", xclicked)
            .attr("cy", yclicked)
            .attr("fill", "red")
            .transition()
            .duration(1000)
            .attr("r", 100)
            .attr("cx", xcenter)
            .attr("cy", ycenter)            
            .each("end", function(){
                j=0;
                while(j<currentcircles.length){
                    d3.select(currentcircles[j][0][0]).remove();

                    j++;
                }
                currentcircles=[];
                console.log(getUrlVars()["name"]);
                generate_circles(pageid,jArray, 800, 500, getUrlVars()["name"]);
                d3.select(this).remove();   
            });
            console.log(currname);
            window.location.assign("pages.php?id="+currid+"&name="+encodeURIComponent(currname));
        });
        

        currentcircles.push(tempcircle);
        
        textInside=sampleSVG.append('text')
        .text(namelist[i])
        .attr("x", xc)
        .attr("y", yc)
        .style("font-size", "25px" );

        currentcircles.push(textInside);

    }

    t=sampleSVG.append("circle")
    .attr("r", 100)
    .attr("cx", xcenter)
    .attr("cy", ycenter)
    .attr("fill", "gray")
    .style("opacity", 0.5);

    currentcircles.push(t);

    tI1=sampleSVG.append('text')
        .text(centertext)
        .attr("x", xcenter)
        .attr("y", ycenter)
        .style("font-size", "25px" );

    currentcircles.push(tI1);

    console.log(currentcircles);
}
if(user)
{
  getUrlVars()["name"]
  generate_circles(pageid,jArray,800,500, getUrlVars()["name"]);
 }  
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}


  </script>

  </body>
</html>