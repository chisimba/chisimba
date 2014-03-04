JODConverter
============

This is JODConverter version 2.2.0, released on 2007-06-03.

JODConverter is a Java library for converting office documents into different
formats, using OpenOffice.org 2.x.

See http://www.artofsolving.com/opensource/jodconverter for the latest documentation.

Before you can perform any conversions you need to start OpenOffice.org
in listening mode on port 8100 as described in the JODConverter Guide.

As a quick start you can type from a command line

  soffice -headless -accept="socket,port=8100;urp;"

JODConverter is both a Java library and a set of ready-to-use tools:

 * a web application that you can deploy into any servlet container (e.g. Apache Tomcat)
 * a command line tool (java -jar jodconverter-cli-2.2.0.jar <input-document> <output-document>)

Requirements
============

The JAR library requires

 * Java 1.4 or higher
 * OpenOffice.org 2.x; recommended 2.0.3 or higher (do NOT use 2.0.2: PDF conversions are broken)

The webapp additionally requires

 * A Servlet 2.3 container such as Apache Tomcat v4.x or higher

Licenses
========

JODConverter is distributed under the terms of the LGPL.

This basically means that you are free to use it in both open source
and commercial projects.

If you modify the library itself you are required to contribute
your changes back, so JODConverter can be improved.

(You are free to modify the sample webapp as a starting point for your
own webapp without restrictions.)

JODConverter includes various third-party libraries so you must
agree to their respective licenses - included in docs/third-party-licenses.

That may include software developed by

 * the Apache Software Foundation (http://www.apache.org)
 * the Spring Framework project (http://www.springframework.org)

--
Mirko Nasato <mirko@artofsolving.com>
