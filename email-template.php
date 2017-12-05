<?php
$email_template = 
//'--'.$boundary_rel.$newline.
////'This is a multi-part message in MIME format.'.$newline.
//'Content-type:multipart/alternative; boundary="'.$boundary_alt.'"'.$newline.
//'MIME-Version: 1.0'.$newline.$newline.
//'--'.$boundary_alt.$newline.
//'Content-Type: text/html; charset=UTF-8'.$newline.
//'MIME-Version: 1.0'.$newline.
//'Content-Transfer-Encoding: 7bit'.$newline.$newline.

'<!DOCTYPE html>
<html>
<head>
<title>Sagesses emails</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<style type="text/css">
/* CLIENT-SPECIFIC STYLES */
#outlook a{padding:0;} /* Force Outlook to provide a "view in browser" message */
.ReadMsgBody{width:100%;} .ExternalClass{width:100%;} /* Force Hotmail to display emails at full width */
.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing */
body, table, td, a{-webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;} /* Prevent WebKit and Windows mobile changing default text sizes */
table, td{mso-table-lspace:0pt; mso-table-rspace:0pt;} /* Remove spacing between tables in Outlook 2007 and up */
img{-ms-interpolation-mode:bicubic;} /* Allow smoother rendering of resized image in Internet Explorer */

/* RESET STYLES */
body{margin:0; padding:0; height:100% !important; margin:0; padding:0; width:100% !important;}
img{border:0; height:auto; line-height:100%; outline:none; text-decoration:none;}
table{border-collapse:collapse !important;}

/* MOBILE STYLES */
@media screen and (max-width: 525px) {

    /* ALLOWS FOR FLUID TABLES */
    table[class="wrapper"]{
        width:100% !important;
    }

    /* ADJUSTS LAYOUT OF LOGO IMAGE */
    td[class="logo"]{
        text-align: left;
        padding: 20px 0 20px 0 !important;
    }

    td[class="logo"] img{
        margin:0 auto!important;
    }

    /* USE THESE CLASSES TO HIDE CONTENT ON MOBILE */
    td[class="mobile-hide"]{
        display:none;}

    img[class="mobile-hide"]{
        display: none !important;
    }

    img[class="img-max"]{
        max-width: 100% !important;
        width: 100% !important;
        height:auto !important;
    }

    /* FULL-WIDTH TABLES */
    table[class="responsive-table"]{
        width:100%!important;
    }

    /* UTILITY CLASSES FOR ADJUSTING PADDING ON MOBILE */
    td[class="padding"]{
        padding: 10px 5% 15px 5% !important;
    }

    td[class="padding-copy"]{
        padding: 10px 5% 10px 5% !important;
        text-align: center;
    }

    td[class="padding-meta"]{
        padding: 30px 5% 0px 5% !important;
        text-align: center;
    }

    td[class="no-pad"]{
        padding: 0 0 20px 0 !important;
    }

    td[class="no-padding"]{
        padding: 0 !important;
    }

    td[class="section-padding"]{
        padding: 50px 15px 50px 15px !important;
    }

    td[class="section-padding-bottom-image"]{
        padding: 50px 15px 0 15px !important;
    }

    /* ADJUST BUTTONS ON MOBILE */
    td[class="mobile-wrapper"]{
        padding: 10px 5% 15px 5% !important;
    }

    table[class="mobile-button-container"]{
        margin:0 auto;
        width:100% !important;
    }

    a[class="mobile-button"]{
        width:80% !important;
        padding: 15px !important;
        border: 0 !important;
        font-size: 16px !important;
    }

}
</style>
</head>
<body style="margin: 0; padding: 0;">

<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tbody><tr>
		<td style="padding: 20px 15px 20px 15px; font-family: Helvetica, Arial, sans-serif;" bgcolor="#ffffff" align="left">
			<table class="responsive-table" style="width: 100%!important" cellspacing="0" cellpadding="0" border="0"><tbody><tr>
				<td style="padding: 0 0 0 0; font-size: 16px; line-height: 25px; color: #666666;" data-type="text" align="left">'.__('You are invited to do this right now:',"sgs-emails").'</td>
			</tr></tbody></table>
		</td>
	</tr></tbody>
</table>

<table style="position: relative; opacity: 1; left: 0px; top: 0px;" cellspacing="0" cellpadding="0" border="0" width="100%"><tbody><tr>
	<td style="padding:  20px 15px 20px 15px; font-family sans-serif;" class="section-padding edit-block" bgcolor="#ffffff" align="left">
		<table class="responsive-table" cellspacing="0" cellpadding="0" border="0" width="500"><tbody><tr>
			<td style="padding: 0 0 0 0;" class="padding-copy" align="left">
			<div data-type="image">
				<img src="cid:'.$related_cid.'" alt="'.$image['alt'].'" style="max-width: 100%!important; width: '.$image['width'].'px!important; height: auto!important; display: block; color: #666666;  font-family: sans-serif; font-size: 16px;" class="img-max" border="0" width="'.$image['width'].'" />
			</div>
			</td>
		</tr></tbody></table>
	</td>
</tr></tbody></table>

</body>
</html>'
//.$newline.$newline.

//'--'.$boundary_alt.'--'.$newline.$newline.

//'--'.$boundary_rel.$newline.
//'Content-Type: '.$image['mime-type'].'; name="'.$image['filename'].'"'.$newline.
//'MIME-Version: 1.0'.$newline.
//'Content-Transfer-Encoding: base64'.$newline.
//'Content-ID: <part1.06090408.01060107>'.$newline.
//'Content-Disposition: inline; filename="'.$image['filename'].'"'.$newline.$newline.

//$img_b64.$newline.$newline.

//'--'.$boundary_rel.'--'
;
?>
