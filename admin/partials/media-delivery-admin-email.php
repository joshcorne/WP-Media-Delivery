<?php
	/**
	 * Provide the meta sidebar area for the plugin admin
	 *
	 * @link       https://joshcorne.co.uk
	 * @since      1.0.0
	 *
	 * @see		   WooCommerce emails
	 * @package    Media_Delivery
	 * @subpackage Media_Delivery/admin/partials
	 */
	?>
<html lang="en-GB">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta content="width=device-width, initial-scale=1.0" name="viewport">
		<title><?php echo esc_html( get_bloginfo( 'name' ) ); ?></title>
		<style type="text/css">@media screen and (max-width: 600px){#header_wrapper{padding: 27px 36px !important; font-size: 24px;}#body_content_inner{font-size: 10px !important;}}</style>
	</head>
	<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: #f7f7f7; padding: 0; text-align: center;" bgcolor="#f7f7f7">
		<table width="100%" id="outer_wrapper" style="background-color: #f7f7f7;" bgcolor="#f7f7f7">
			<tr>
				<td>
					<!-- Deliberately empty to support consistent sizing and layout across multiple email clients. -->
				</td>
				<td width="600">
					<div id="wrapper" dir="ltr" style="margin: 0 auto; padding: 70px 0; width: 100%; max-width: 600px; -webkit-text-size-adjust: none;" width="100%">
						<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
							<tr>
								<td align="center" valign="top">
									<div id="template_header_image">
										<p style="margin-top: 0;">
											<?php if( has_custom_logo() ) { ?>
												<img src="<?php echo esc_url( wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'medium' )[0] ) ?>" 
													style="border: none; display: inline-block; font-size: 14px; font-weight: bold; height: auto; outline: none; 
													text-decoration: none; text-transform: capitalize; vertical-align: middle; max-width: 100%; margin-left: 0; 
													margin-right: 0;" border="0" />
											<?php } else { ?>
												<h1>
													<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
												</h1>
											<?php } ?>
										</p>
									</div>
									<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_container" style="background-color: #fff; border: 1px solid #dedede; box-shadow: 0 1px 4px rgba(0,0,0,.1); border-radius: 3px;" bgcolor="#fff">
										<tr>
											<td align="center" valign="top">
												<!-- Header -->
												<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_header" style='background-color: <?php echo esc_attr( $bg_color ); ?>; color: #fff; border-bottom: 0; font-weight: bold; line-height: 100%; vertical-align: middle; font-family: "Helvetica Neue",Helvetica,Roboto,Arial,sans-serif; border-radius: 3px 3px 0 0;' bgcolor="<?php echo esc_attr( $bg_color ); ?>">
													<tr>
														<td id="header_wrapper" style="padding: 36px 48px; display: block;">
															<h2 style='font-family: "Helvetica Neue",Helvetica,Roboto,Arial,sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; text-align: left; text-shadow: 0 1px 0 #9976c2; color: <?php echo esc_attr( '' ); ?>; background-color: inherit;' bgcolor="inherit"><?php echo esc_html( $subject ) ?></h2>
														</td>
													</tr>
												</table>
												<!-- End Header -->
											</td>
										</tr>
										<tr>
											<td align="center" valign="top">
												<!-- Body -->
												<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_body">
													<tr>
														<td valign="top" id="body_content" style="background-color: #fff;" bgcolor="#fff">
															<!-- Content -->
															<table border="0" cellpadding="20" cellspacing="0" width="100%">
																<tr>
																	<td valign="top" style="padding: 48px 48px 32px;">
																		<div id="body_content_inner" style='color: #636363; font-family: "Helvetica Neue",Helvetica,Roboto,Arial,sans-serif; font-size: 14px; line-height: 150%; text-align: left;' align="left">
																			<p style="margin: 0 0 16px;"><?php echo wp_kses_post( $message ) ?></p>
																		</div>
																	</td>
																</tr>
															</table>
															<!-- End Content -->
														</td>
													</tr>
												</table>
												<!-- End Body -->
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td align="center" valign="top">
									<!-- Footer -->
									<table border="0" cellpadding="10" cellspacing="0" width="100%" id="template_footer">
										<tr>
											<td valign="top" style="padding: 0; border-radius: 6px;">
												<table border="0" cellpadding="10" cellspacing="0" width="100%">
													<tr>
														<td colspan="2" valign="middle" id="credit" style='border-radius: 6px; border: 0; color: #8a8a8a; font-family: "Helvetica Neue",Helvetica,Roboto,Arial,sans-serif; font-size: 12px; line-height: 150%; text-align: center; padding: 24px 0;' align="center">
															<p style="margin: 0 0 16px;"><?php echo esc_html( get_bloginfo( 'name' ) ) ?></p>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									<!-- End Footer -->
								</td>
							</tr>
						</table>
					</div>
				</td>
				<td>
					<!-- Deliberately empty to support consistent sizing and layout across multiple email clients. -->
				</td>
			</tr>
		</table>
	</body>
</html>