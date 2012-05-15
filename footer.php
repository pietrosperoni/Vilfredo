
			</div>
			<div id="footer">
				<p><em><?=$VGA_CONTENT['madeby_txt']?> <a href="http://pietrosperoni.it/">Pietro</a> &amp; Derek.</em></p>
			</div>
			
			<?php echo facebook_fbconnect_init_js(DISPLAY_FACEBOOK_LOGIN); ?>
			
		</body>
	</html>
	<?php
		ob_end_flush();
	?>