
			</div>
			<div id="footer">
				<p><em><?=$VGA_CONTENT['madeby_txt']?> <a href="http://turnfront.com/">Chris</a>, <a href="http://pietrosperoni.it/">Pietro</a> &amp; Derek.</em></p>
			</div>
			
			<!--FB Start*****-->
			<?php echo facebook_fbconnect_init_js(); ?>
			<!-- //*****FB End-->
			
		</body>
	</html>
	<?php
		ob_end_flush();
	?>