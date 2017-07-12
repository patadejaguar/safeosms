<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="html" encoding="iso-8859-1" indent="no"/>



<xsl:include href="../common/PHPReportPage.xsl"/>
<xsl:include href="../common/PHPReportRow.xsl"/>
<xsl:include href="../common/PHPReportCol.xsl"/>
<xsl:include href="../common/PHPReportXHTML.xsl"/>
<xsl:include href="../common/PHPReportBookmark.xsl"/>
<xsl:include href="../common/PHPReportImg.xsl"/>
<xsl:include href="../common/PHPReportCSS.xsl"/>


<xsl:param name="sql"/>
<xsl:param name="user"/>			
<xsl:param name="pass"/>
<xsl:param name="conn"/>
<xsl:param name="interface"/>
<xsl:param name="database"/>
<xsl:param name="nodatamsg"/>

<xsl:template match="/">
	<xsl:apply-templates/>
</xsl:template>


<xsl:template match="RP">
	<HTML>
		<HEAD>
			<TITLE><xsl:value-of select="@TITLE"/></TITLE>
			<xsl:if test="string-length(@CSS)>0">
				<LINK REL="stylesheet" TYPE="text/css">
					<xsl:attribute name="HREF">
						<xsl:value-of select="@CSS"/>
					</xsl:attribute>
				</LINK>	
			</xsl:if>
			<xsl:call-template name="CSS_MEDIA"/>
		</HEAD>
		<BODY>
			<xsl:if test="string-length(@BGCOLOR)>0">
				<xsl:attribute name="BGCOLOR">
					<xsl:value-of select="@BGCOLOR"/>
				</xsl:attribute>	
			</xsl:if>
			<xsl:if test="string-length(@BACKGROUND)>0">
				<xsl:attribute name="BACKGROUND">
					<xsl:value-of select="@BACKGROUND"/>
				</xsl:attribute>	
			</xsl:if>
			<xsl:apply-templates select="/RP/PG" />
			
			
			
			<!--  <xsl:apply-templates select="/RP/PG[number(@PN)=$curpage]"/>
			<br clear="all"/>
			<xsl:call-template name="PAGELINKS"/> -->
		</BODY>
	</HTML>
</xsl:template>




</xsl:stylesheet>
