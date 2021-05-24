1) Working example at https://bridgmco.dev.fast.sheridanc.on.ca/showcase/treb_geolocate.php

2) Coordinates are periodically updated from google to correct "drift"
2.1) Updates are done via a custom Python program which reads from a .XML file, gathered from google.ca/mymaps, and writes a .JSON

3) Calculation is run in JavaScript to maximize performance by removing latency

4) Logic Explanation:
 i) The Location in question is tested via "General Coordinates" using Max/Min values included in the .JSON structure
 * This "General Matching" allows instant elimination of the majority of districts
 ** If only one match is found -> District is found, report results
 ** If no matches are found -> Location is outside Toronto
 ** If more than 1 match is found -> Move to phase ii
 
 ii) The target location will be tested against every line in every district polygon which is a general match
 * Drawing a line straight north from the location, how many polygon lines does it collide with? Same for east.
 ** An EVEN number of collisions indicates the point lands OUTSIDE the polygon
 ** An ODD number of collisions indicates the point lands INSIDE the polygon
 *** A location with ODD North and ODD East collisions with a district is therefore inside that district
