<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="/">
		<div id="PatientListModule" class="PatientListModule">
			<xsl:for-each select="module/patient">
				<div>
					<img src="/chart/resources/images/non-avatar.jpg" width="50" height="50" alt="no avatar"/>
					<xsl:value-of select="firstName"/>
					<xsl:text> </xsl:text>
					<xsl:value-of select="lastName" />
					<xsl:text> in room #</xsl:text>
					<xsl:value-of select="roomNumber" />
					<xsl:text> in unit </xsl:text>
					<xsl:value-of select="unitName" />
					<ul class="flattenList">
						<li><a href="kardex.php">Got to the Kardex</a> |</li>
						<li><a href="assessments.php">View Assessments</a> |</li>
						<li><a href="orders.php">View Orders</a> |</li>
						<li><a href="patient/{patientId}/MARPage">View the Patient's MAR</a></li>
					</ul>
				</div>
			</xsl:for-each>
		</div>
	</xsl:template>
</xsl:stylesheet>