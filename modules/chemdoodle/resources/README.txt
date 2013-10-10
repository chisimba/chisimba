ChemDoodle Web Components

ChemDoodle Web Components are pure javascript objects derived from
ChemDoodle (www.ChemDoodle.com) to solve common chemistry related
tasks on the web. These components are powerful, fully customizable,
easy to implement, work on all major browsers including those on
iPhone OS and Android, and are free under the GPL Version 3 license.

See the PHP web page files in the "samples" directory for examples
of how to use the components on your website.

A few things to keep in mind:
- In order to use the components, you must use a recent browser.
  Specifically, Safari 4, Firefox 3.5, Google Chrome 2+, or Internet
  Explorer 6+. Also, Mobile Safari and Android browser are supported.
  Opera does not yet fully support the canvas tag.
- The canvas tag of these browsers can be CPU intensive. So performance
  may vary depending on the CPU and memory available on your computer.
- 3D Components based on WebGL will only work in development browsers at
  this time.
- The ChemDoodleWeb PHP scripts are provided as examples only. They are not
  meant to be used in a production application to access a production
  database. It is up to the user to make it robust and secure.

Downloaded zip file contents:
- ChemDoodleWeb.js          ==> ChemDoodle Web Components javascript (packed)
- ChemDoodleWeb-unpacked.js ==> ChemDoodle Web Components javascript (unpacked)
- ChemDoodleWeb-libs.js     ==> Third party javascript libraries (packed);
- ChemDoodleWeb.css         ==> Basic CSS file
- CDWPubChem.php            ==> PHP script that queries PubChem with the specified query
- CDWGetFile.php            ==> PHP script that reads a user specified file
- CDWfile2js.php            ==> PHP script that converts a mol file to a javascript string
- samples                   ==> Directory containing sample PHP web page files
- molecules                 ==> Directory containing test molecule files
- spectra                   ==> Directory containing test spectrum files
- README.txt                ==> This readme file
- COPYING.txt               ==> GNU GPL Version 3 license

If you have any questions about ChemDoodle Web Components, please contact us at
http://www.ichemlabs.com/contact-us .

If you use ChemDoodle Web Components in your website, we'd appreciate it
if you would provide a link on your site to web.ChemDoodle.com and/or www.ChemDoodle.com.
