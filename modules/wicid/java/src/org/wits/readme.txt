CONFIGURATION STEPS
==========

1. Copy the src folder from localhost/chisimba/packages/wicid to your project directory (for the rest of this tutorial use your project directory instead of yourFolderDir).

2. Copy Wicid.html and Wicid.css from localhost/chisimba/packages/wicid/java/src/org/wits into yourProjectDir/Wicid/war

3. Open yourProjectDir/Wicid/war/Wicid.html in gedit. Change script links to css and extjs to the correct one for your project. 

4. Copy web.xml from localhost/chisimba/packages/wicid/java/src/org/wits into yourProjectDir/Wicid/war/WEB-INF

5. Copy gxt.jar from where you saved your gxt folder into yourProjectDir/Wicid/war/WEB-INF/lib

6. Open yourProjectDir/Wicid/build.xml in gedit. Change gwt.sdk property in to the correct path where you saved your gwt folder and the chage gwt.properties path to /yourProjectDir/Wicid/src/org/wits/gwt.properties

7 In netbeans open Wicid/src/org.wits.server/ChisimbaServlet.java, go to line 28 and change the path to your Chisimba path

8. In netbeans open Wicid/src/org.wits.client/Constants.java, go to line 12 and change the constants MAIN_URL patter to the correct path

9. In the terminal compile ant gwtc by going to your project directory and typing 'ant gwtc'. This will take a few minutes.

10. Open firefox and go to chisimba (localhost/chisimba). Go to 'Admin' then 'Site Administartion' then 'System Configuration'. Scroll down to wicid and click on it. Click on FILES_DIR, change the parameter value to where you want wicid to save your files to and save (make sure the folder where you're saving to exists). Change the file permissions on this folder sudo chmod -R 777 /...(your folder's path)

11. Click on MODE, change the parameter value to 'apo' and save.

12. Refresh wicid.