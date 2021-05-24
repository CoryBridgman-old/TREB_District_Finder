<?php 
    $get_data = file_get_contents('location/of/district/coordinates.json'); 
    $district_data = json_decode($get_data, true);
  ?>
  
<script>
    const districts = <?php echo json_encode($district_data) ?>;
    var test_dist = [];
    var point_lng;
    var point_lat;

    function search_dist(){
      test_dist = [];
      point_lng = parseFloat(document.getElementById('lng').value);
      point_lat = parseFloat(document.getElementById('lat').value);

      for (dist in districts){
        if ((point_lng <= districts[dist]['highs']['lng'])&&(point_lng >= districts[dist]['lows']['lng'])&&(point_lat <= districts[dist]['highs']['lat'])&&(point_lat >= districts[dist]['lows']['lat'])){
          test_dist.push(districts[dist]);
        }
      }
      console.log(test_dist.length);
      console.log(test_dist);
      if(test_dist.length == 0){ //Not in City
        document.getElementById('district').innerHTML = 'Target isn\'t in Toronto';
      }else{ //Further refining required
        locate_dist(point_lat, point_lng, test_dist);
      }
    }

    function locate_dist(point_lat, point_lng, dist){
      var failed_to_locate = false;

      var start_lng;
      var end_lng;
      var start_lat;
      var end_lat;

      var vPosition;
      var hPosition;

      var coord_length;
      var n_Intercept;
      var e_Intercept;

      for(i in dist){ //loop once for each district
        coord_length = dist[i]['coordinates'].length;
        n_Intercept = 0;
        e_Intercept = 0;
        for(var n = 0; n < coord_length; n++){ //check the number of collisions with district borders (-ve is in, +ve is out)

          start_lng = dist[i]['coordinates'][n]['lng'];
          start_lat = dist[i]['coordinates'][n]['lat'];
          if(n < (coord_length-1)){
            end_lng = dist[i]['coordinates'][n+1]['lng'];
            end_lat = dist[i]['coordinates'][n+1]['lat'];
          }else{ //final segment
            end_lng = dist[i]['coordinates'][0]['lng'];
            end_lat = dist[i]['coordinates'][0]['lat'];
          }
          
          // Line segment vertical position relative to point
          if(point_lat <= start_lat && point_lat <= end_lat){ //line above point
            vPosition = 1;
          }else if(point_lat >= start_lat && point_lat >= end_lat){ //line below point
            vPosition = -1;
          }else{ //line across point
            vPosition = 0;
          }

          // Line segment horizontal position relative to point
          if(point_lng <= start_lng && point_lng <= end_lng){ //line right of point
            hPosition = 1;
          }else if(point_lng >= start_lng && point_lng >= end_lng){ //line left of point
            hPosition = -1;
          }else{ //line across point
            hPosition = 0;
          }

          // Check for North and East collisions
          if (hPosition == 0){
            if(vPosition == 0){
              var m = ((end_lat-start_lat)/(end_lng-start_lng));
              var b = start_lat-(m*start_lng);
              var y = (m*point_lng) + b;
              var x = ((point_lat - b)/m)
              if(y >= point_lat){
                n_Intercept += 1;              
              }
              if(x >= point_lng){
                e_Intercept += 1;
              }
            }else if(vPosition == 1){
              n_Intercept += 1;
            }
          }else if (hPosition == 1 && vPosition == 0){
            e_Intercept += 1;
          }
        }
        
        console.log(dist[i]['dist_id']+" e_int: " +e_Intercept);
        if((n_Intercept%2 == 1) && (e_Intercept%2 ==1)){ //Either N or E are enough, using both will help cause (and catch) failures
          document.getElementById('district').innerHTML = 'District ' + dist[i]['dist_id'];
          failed_to_locate = false;
          break;
        }else{
          failed_to_locate = true;
        }
      }
      if(failed_to_locate){
          document.getElementById('district').innerHTML = 'Failed to locate';
      }
    }
    function showHelp(){
      alert("Input any Google Maps coordinates to see which TREB district they belong to.");
    }
  </script>
