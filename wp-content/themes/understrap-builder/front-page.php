<?php
get_header();
$hero_image = get_field('hero_image');

?>
<main>
	<section class="hero">
		<div class="container">
			<div class="row">
				<div class="col-12 hero-img-block">
					<img src="<?php echo $hero_image['url'];?>" alt="<?php echo $hero_image['alt']; ?>" title="<?php echo $hero_image['title']; ?>"/>
				</div>
				<div class="hero-title-block">
					<div class="title"><?php the_field('hero_title'); ?></div>
					<a class="btn btn-main" href="<?php the_field('hero_button_link'); ?>"><?php the_field('hero_button_text'); ?></a>
				</div>
				<div class="scroll-down-block">
					<span class="btn-down"><img src="/wp-content/themes/understrap-builder/img/arrow-down.png" alt="arrow-down"><span><?php the_field('scroll_down_button_text'); ?></span></span>
				</div>
			</div>
		</div>
	</section>
	<section class="vehicle-grid">
		<div class="container">
			<div class="row">
				<div class="col-12 title-block">
					<div class="title">More then 500+ vehicles for you to choose from</div>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-4 filter-item">
					<select id="manuf">
						<option value="all">Select Manufacturer</option>
						<?php 
                        $terms = get_terms( array(
                            'taxonomy' => 'manufacturer',
                            'hide_empty' => false,
                        ) ); 
                        foreach($terms as $term):
						
						?>
						<option value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-4 filter-item">
					<select id="class">
						<option value="all">Select Class</option>
						<?php 
                        $terms = get_terms( array(
                            'taxonomy' => 'class',
                            'hide_empty' => false,
                        ) ); 
                        foreach($terms as $term):
						?>
						<option value="<?php echo $term->slug; ?>"><?php echo $term->name; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-4 filter-button">
					<span id="filter">Filter</span>
				</div>
			</div>
		</div>
		<div class="container container-grid">
			<div id="result" class="row">
				<?php
					$args=array(
						'posts_per_page' => 6,
						'post_status' => 'publish',
						'post_type' => 'vehicle',
						'meta_key' => 'price',
						'orderby' => 'meta_value',
						'order' => 'ASC'
					);
					$query = new WP_Query($args);
					if ($query->have_posts()):
					while ($query->have_posts()): $query->the_post();
					$id = get_the_ID();
					$manuf = wp_get_post_terms($id, 'manufacturer', array("fields" => "all"));
					$class = wp_get_post_terms($id, 'class', array("fields" => "all"));
				?>
				<div class="col-4 vehicle-item">
					<div class="wrap">
						<div class="img-block">
							<?php echo get_the_post_thumbnail($post); ?>
						</div>
						<div class="row desc-block">
							<div class="col-12 title">
								<?php the_title(); ?>
							</div>
							<div class="col-7 desc-items">
								<div class="year"><span>Year:</span> <?php the_field('year'); ?></div>
								<div class="manufacturer"><span>Manufacturer:</span> <?php echo $manuf[0]->name; ?></div>
								<div class="class"><span>Class:</span> <?php echo $class[0]->name; ?></div>
							</div>
							<div class="col-5  price">
								<span>$<?php the_field('price'); ?></span>
							</div>
						</div>
					</div>
				</div>
				<?php
					endwhile;
					endif;
					wp_reset_postdata();
                ?>
			</div>
		</div>
		<div class="load-more-block">
			<div id="controler" data-manuf="all" data-class="all" data-page="1"></div>
			<span class="btn-load-more">Load More</span>
		</div>
	</section>
</main>
<script>
	jQuery(document).ready(function($) {
		var ajaxurl = '<?php echo admin_url("admin-ajax.php") ?>';
		$('#filter').click(function(){
			$('#controler').attr('data-page', 1);
			var v_manuf = $('#manuf').val();
			var v_class = $('#class').val();
			$('#controler').attr('data-manuf', v_manuf);
			$('#controler').attr('data-class', v_class);
			var data = {
				'action': 'filter_callback',
				'manuf':v_manuf,
				'class':v_class,
			};
			$.post(ajaxurl, data, function(response) {
				$('#result').html(response);
			});
		});
		$('.btn-load-more').click(function(){
			var paged = $('#controler').attr('data-page');
			paged++;
			$('#controler').attr('data-page', paged);
			var v_manuf = $('#controler').attr('data-manuf');
			var v_class = $('#controler').attr('data-class');
			var data = {
				'action': 'filter_callback',
				'manuf':v_manuf,
				'class':v_class,
				'paged':paged
			};
			$.post(ajaxurl, data, function(response) {
				$('#result').append(response);
			});
		});
		$('.btn-down').click(function(){
			$('html, body').animate({
            	scrollTop: $('.vehicle-grid').offset().top
        	}, 'slow');
		});
	});
</script>
<?php get_footer(); ?>