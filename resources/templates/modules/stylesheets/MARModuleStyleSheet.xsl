<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="/">
		<ul id="mar_menu" class="tabs">
			<li class="tab">MAR</li>
			<li class="tab">Active</li>
			<li class="tab">PRN</li>
			<li class="tab">View by Time</li>			
		</ul>
		<div id="MARModule" class="MARModule">
			<input type="hidden" id="mar_administer_patientId" name="patientId" value="{//patientId}" />
			<xsl:for-each select="module/medication">
				<div class="module_element" id="medicationToAdminister_{medicationId}_container">
					<xsl:if test="confirmed != 1">
						<xsl:attribute name="class">module_element unconfirmedMedication</xsl:attribute>
					</xsl:if>			
					<xsl:choose>
						<xsl:when test="confirmed = 1">
							<a href="administer/{medicationId}" id="medicationToAdminister_{medicationId}" medicationId="{medicationId}" drug="{genericName}" dosage="{commonDose}{unit}" class = "action_administer_message">
								<xsl:value-of select="genericName"/>
							</a>
							<xsl:text> </xsl:text>
							<xsl:value-of select="commonDose" />
							<xsl:value-of select="unit" />			
							<ul class="flattenList">
								<li>
									<a href="administer/{medicationId}" id="medicationToAdminister_{medicationId}" medicationId="{medicationId}" drug="{genericName}" dosage="{commonDose}{unit}" class = "action_administer_message">Administer</a> |
								</li>
								<li>
									<a href="administer/hold/{medicationId}" medicationId="{medicationId}" drug="{genericName}" dosage="{commonDose}{unit}" class = "action_hold_message">Hold</a> |
								</li>
								<li>
									<a href="message/pharmacy/{medicationId}" medicationId="{medicationId}" drug="{genericName}" dosage="{commonDose}{unit}" class = "action_send_message">Send Pharmacy a Message</a>
								</li>
							</ul>
						</xsl:when>
						<xsl:otherwise>
							<a href="administer/confirm/{medicationId}" medicationId="{medicationId}" drug="{genericName}" dosage="{commonDose}{unit}" class = "action_confirm_message">
								<xsl:value-of select="genericName"/>
							</a>
							<xsl:text> </xsl:text>
							<xsl:value-of select="commonDose" />
							<xsl:value-of select="unit" />	
							<ul class="flattenList">
								<li>
									This medication must be confirmed before being administered.  <a href="administer/confirm/{medicationId}" medicationId="{medicationId}" drug="{genericName}" dosage="{commonDose}{unit}" class = "action_confirm_message">Confirm</a>
								</li>
							</ul>
						</xsl:otherwise>
					</xsl:choose>
				</div>
			</xsl:for-each>
		</div>
	</xsl:template>
</xsl:stylesheet>