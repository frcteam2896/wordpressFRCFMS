Plugin Name: FRCFMS widget<br>
Plugin URI: https://github.com/MechaMonarchs/wordpressFRCFMS<br/>
Version: 0.1.4<br/>
Author: Damien MechaMonarchs (FRC Team 2896)<br/>
Description: Integrates data from FRCFMS, a score reporting site for FIRST Robotics<br/>
Changelog:<br/>
+v0.1.4<br/>
+Code Documentation<br/>
+TBA_Parse() now returns error codes in an array instead of just NULL<br/>
+FMS_Split() to handle parsing of data passed on by TBA_Parse()<br/>
+Seperate error messages<br/>
+Utilization of initialized variables in settings function<br/>
+FMS_Split() can take match numbers (in terms of most recent matches) as argument<br/>
-Data parsing from TBA_Parse() (Confusing, no? I'll probably change function names later)<br/>