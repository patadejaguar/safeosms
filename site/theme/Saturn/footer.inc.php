	<footer>
		<div class="container">
			<div class="units-row">
			    <div class="unit-50">
				<?php get_component('sidebar'); ?>
			    </div>
			    <div class="unit-50">
				<?php get_component('sidebar2'); ?>
			    </div>
			</div>
			<p class="text-centered foot-cp">
	    		<?php get_site_credits(); ?> <?php return_site_ver(); ?> | Theme: <a href="http://glowczynski.pl" target="_blank">Arthur Glovchynski</a>
	    	</p>
		</div>
	</footer>

	<!-- Javascript -->
	<script src="<?php get_theme_url(); ?>/js/jquery.min.js"></script>
    <script src="<?php get_theme_url(); ?>/js/kube.min.js"></script>
    <?php get_footer(); ?>
</body>
</html>