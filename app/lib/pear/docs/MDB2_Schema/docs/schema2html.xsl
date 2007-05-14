<?xml version="1.0" encoding="UTF-8" ?>
<!--
 **
 * MDB2_Schema : XSL Transform to render a MDB2 Schema into html
 * Presentation is done with the schema2html.css style sheet
 *
 * @category   DB
 * @package    MDB2_Schema
 * @author     Bertrand Gugger <bertrand@toggg.com>
 * @copyright  2006 bertrand Gugger
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD license
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/MDB2_Schema
 *
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="/">
    <!-- Untill the body -->
    <html>
     <head>
      <title>MDB2 Schema Documentation</title>
      <link rel="stylesheet" type="text/css" href="schema2html.css" />
     </head>
    <body>
    <!-- Which database is used -->
    <div class="tablemain">
    <table>
    <tr class="database"><td colspan="4">
     <span class="label">database</span>
     <span class="name"><xsl:value-of select="database/name"/></span>
    </td><td colspan="4">
     <span class="description"><xsl:value-of select="database/description"/></span>
    </td><td colspan="1">
     <span class="comments"><xsl:copy-of select="database/comments"/></span>
    </td></tr>
    <tr class="menu"><td colspan="8">
    <xsl:for-each select="database/table">
       <a>
        <xsl:attribute name="href">#<xsl:value-of select="name"/></xsl:attribute>
        <xsl:value-of select="name"/>
       </a><xsl:text> </xsl:text>
    </xsl:for-each>
    </td><td colspan="1" align="right">
     <span class="comments">N=notnull, A=autoincrement, +=unsigned, U=unique, P=primary</span>
    </td></tr>

    <xsl:for-each select="database/table">
        <xsl:call-template name="showtable"/>
    </xsl:for-each>
    </table>
    </div>
    </body></html>
</xsl:template>

<!-- Once there were tables -->
<xsl:template name="showtable">
    <tr><td colspan="2" height="18"></td></tr>
    <tr class="tableheader"><td colspan="6">
       <a>
        <xsl:attribute name="name"><xsl:value-of select="name"/></xsl:attribute>
     <span class="label">table</span>
     <span class="name"><xsl:value-of select="name"/></span>
       </a>
    </td><td colspan="3">
     <span class="description"><xsl:value-of select="description"/></span>
    </td></tr>
    <tr><td class="comments" colspan="9">
     <xsl:copy-of select="comments"/>
    </td></tr>

<!-- They had fields -->
    <tr class="tableheader label">
    <td>field</td><td>type</td><td>indexes</td>
    <td style="text-align: center">N</td>
    <td style="text-align: center">A</td>
    <td style="text-align: center">+</td>
    <td>description</td><td>default</td><td>comments</td></tr>

    <xsl:for-each select="descendant::declaration/field">
        <xsl:call-template name="showfield"/>
    </xsl:for-each>

<!-- Then made some order on them -->
    <xsl:choose>

    <xsl:when test="declaration/index">
    <tr class="tableheader label">
    <td colspan="2">indexes</td>
    <td style="text-align: center">^ ^ ^</td>
    <td style="text-align: center">U</td>
    <td style="text-align: center">P</td>
    <td colspan="3">Involved fields</td>
    <td>comments</td></tr>

    <xsl:for-each select="descendant::declaration/index">
        <xsl:call-template name="showindex"/>
    </xsl:for-each></xsl:when>
    
    <xsl:otherwise>
    <tr class="tableheader label">
    <td colspan="9">No index, it's single (parameters) or flat (list)</td></tr>
    </xsl:otherwise>

    </xsl:choose>

</xsl:template>

<!-- And in <table> some <field> -->
<xsl:template name="showfield">
    <tr class="tablebody">
    <td><span class="name"><xsl:value-of select="name"/></span></td>
    <td><span class="type"><xsl:value-of select="type"/>
        <xsl:for-each select="descendant::length"><xsl:call-template name="showlength"/>
        </xsl:for-each>
    </span></td>
    <td class="indexes">
    <xsl:variable name="curfield" select="current()/name"/>
    <xsl:for-each select="../index">
         <xsl:choose>
         <xsl:when test="field[name = $curfield]"><xsl:for-each select="field[name = $curfield]">
         <xsl:choose>
            <xsl:when test="sorting = 'descending'">-</xsl:when>
            <xsl:otherwise>+</xsl:otherwise></xsl:choose>
            <xsl:number count="index" format="A"/><xsl:number count="index/field" format="1"/>
         </xsl:for-each></xsl:when>
         <xsl:otherwise>&#160;&#160;&#160;</xsl:otherwise>
         </xsl:choose>
    </xsl:for-each>
    </td>
    <td><span class="attributes">
        <xsl:if test="descendant::notnull != 0">N</xsl:if>
    </span></td>
    <td><span class="attributes">
        <xsl:if test="descendant::autoincrement != 0">A</xsl:if>
    </span></td>
    <td><span class="attributes">
        <xsl:if test="descendant::unsigned != 0">+</xsl:if>
    </span></td>
    <td><span class="description"><xsl:value-of select="description"/></span></td>

    <td><span class="default"><xsl:value-of select="default"/></span></td>
    <td><span class="comments"><xsl:copy-of select="comments"/></span></td>
    </tr>
</xsl:template>

<!-- <length> for some <type> -->
<xsl:template name="showlength">(<xsl:value-of select="//length"/>)
</xsl:template>

<!-- So <table> has a <declaration> with few <field>
     and <index> refering these <field> in the same <table> -->
<xsl:template name="showindex">
    <tr class="tablebody"><td colspan="2"><span class="name"><xsl:value-of select="name"/>
    </span></td><td colspan="1" class="indexes">
        <xsl:variable name="curindex" select="current()/name"/>
    <xsl:for-each select="../index">
         <xsl:choose>
         <xsl:when test="$curindex = name">&#160;<xsl:number format="A"/>&#160;</xsl:when>
         <xsl:otherwise>&#160;&#160;&#160;</xsl:otherwise>
         </xsl:choose>
    </xsl:for-each >
    </td><td colspan="1"><span class="attributes">
        <xsl:if test="unique != 0">U</xsl:if>
    </span></td><td colspan="1"><span class="attributes">
        <xsl:if test="primary != 0">P</xsl:if>
    </span></td><td colspan="3"><span class="indexfield">
        <xsl:for-each select="field">
            <xsl:choose>
            <xsl:when test="sorting = 'descending'">-</xsl:when><xsl:otherwise>+</xsl:otherwise></xsl:choose><xsl:value-of select="name"/>&#160;
        </xsl:for-each>
    </span></td><td colspan="1"><span class="comments"><xsl:copy-of select="comments"/>
    </span></td></tr>
</xsl:template>

</xsl:stylesheet>
