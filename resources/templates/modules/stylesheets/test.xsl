<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="/">
			<xsl:for-each select="list/mc">
				<xsl:value-of select="name"/>
				<xsl:value-of select="cost"/>
			</xsl:for-each>
	</xsl:template>
</xsl:stylesheet>