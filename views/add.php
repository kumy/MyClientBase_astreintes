<?php $this->load->view('dashboard/header', array('header_insert'=>array('invoices/invoice_edit_header'))); ?>

<?php $this->load->view('dashboard/jquery_date_picker'); ?>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/jquery/jquery.relcopy.js"></script>

<script type="text/javascript">
	$(function(){
		var append_to_clone = ' <a class="remove" href="#" onclick="$(this).parent().remove(); return false"><?php echo $this->lang->line('delete'); ?></a>';
		$('a.copy').relCopy({append: append_to_clone});
		$('#tabs').tabs({ selected: <?php echo $tab_index; ?> });
	});
</script>

<div class="grid_10" id="content_wrapper">

	<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>">

	<div class="section_wrapper">

		<h3 class="title_black"><?php echo $this->lang->line('astreinte_id'); ?><?php echo $astreinte_id; ?>

                        <span style="font-size: 60%;">
                        <input type="submit" name="btn_return_to_list" class="uibutton" style="float: right; margin-top: 10px; margin-right: 10px;" value="<?php echo $this->lang->line('return_to_list'); ?>" />
                        <input type="submit" name="btn_add_new_ticket" class="uibutton" style="float: right; margin-top: 10px; margin-right: 10px;" value="<?php echo $this->lang->line('add_astreinte_ticket'); ?>" />
            		<!--input type="submit" name="btn_copy_astreinte" class="uibutton" style="float: right; margin-top: 10px; margin-right: 10px;" value="<?php echo $this->lang->line('copy'); ?>" /-->
            		<input type="submit" name="btn_regen_ticket_times" class="uibutton" style="float: right; margin-top: 10px; margin-right: 10px;" value="<?php echo $this->lang->line('regen_ticket_times'); ?>" />
                        </span>
                        
                </h3>


		<?php $this->load->view('dashboard/system_messages'); ?>

		<div class="content toggle">
			<div id="tabs">
				<ul>
					<li><a href="#tab_general"><?php echo $this->lang->line('summary'); ?></a></li>
					<li><a href="#tab_tickets"><?php echo $this->lang->line('tickets'); ?></a></li>
				</ul>
				<div id="tab_general">
					<?php $this->load->view('tab_general'); ?>
				</div>

				<div id="tab_tickets">
					<?php $this->load->view('tab_tickets_table'); ?>
				</div>
			</div>
		</div>

	</div>

	</form>

</div>

<?php $this->load->view('dashboard/footer'); ?>
