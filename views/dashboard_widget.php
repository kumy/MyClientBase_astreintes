	<div class="section_wrapper">

		<h3 class="title_black"><?php echo $this->lang->line('open_astreintes'); ?></h3>

		<div class="content toggle no_padding">

			<?php if ($astreintes) foreach ($astreintes as $astreinte) echo display_currency ($astreinte->total); ?>

		</div>

	</div>
