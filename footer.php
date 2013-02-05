
			</div>
			<div id="footer">
				<p><em><?=$VGA_CONTENT['madeby_txt']?> <a href="http://pietrosperoni.it/">Pietro</a> &amp; Derek. For bugs and suggestions please contact Pietro on twitter: <a href="http://twitter.com/pietrosperoni">@pietrosperoni</a></em></p>
			</div>
			
			<?php echo facebook_fbconnect_init_js(DISPLAY_FACEBOOK_LOGIN); ?>
			
		</body>
	</html>
	<?php
		ob_end_flush();
	?>