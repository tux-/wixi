<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="xml" indent="no"/>

	<xsl:preserve-space elements="*"/>

	<xsl:template match="*">
		<xsl:apply-templates/>
	</xsl:template>

	<xsl:template match="/html/body/*">
		<xsl:copy>
			<xsl:apply-templates/>
		</xsl:copy>
	</xsl:template>

	<xsl:template match="i|em">
		<xsl:element name="i">
			<xsl:apply-templates/>
		</xsl:element>
	</xsl:template>

	<xsl:template match="b|strong">
		<xsl:element name="b">
			<xsl:apply-templates/>
		</xsl:element>
	</xsl:template>

	<xsl:template match="s">
		<xsl:element name="s">
			<xsl:apply-templates/>
		</xsl:element>
	</xsl:template>

	<xsl:template match="a">
		<xsl:element name="a">
			<xsl:attribute name="href">
				<xsl:value-of select="@href"/>
			</xsl:attribute>
			<xsl:apply-templates/>
		</xsl:element>
	</xsl:template>

	<xsl:template match="br">
		<xsl:element name="br">
			<xsl:apply-templates/>
		</xsl:element>
	</xsl:template>

	<xsl:template match="u">
		<xsl:element name="u">
			<xsl:apply-templates/>
		</xsl:element>
	</xsl:template>

	<xsl:template match="code">
		<xsl:choose>
			<xsl:when test="@class='inline-code'">
				<xsl:element name="code-inline">
					<xsl:apply-templates/>
				</xsl:element>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="mark">
		<xsl:choose>
			<xsl:when test="@class='cdx-marker'">
				<xsl:element name="marker">
					<xsl:apply-templates/>
				</xsl:element>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

</xsl:stylesheet>
