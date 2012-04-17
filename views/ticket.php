<?php $this->load->view('dashboard/header'); ?>

<!--script type="text/javascript" src="http://trentrichardson.com/examples/timepicker/js/jquery-ui-timepicker-addon.js"></script-->
<script type="text/javascript">
        $(function() {
                //$("#datetimepicker").datetimepicker();
                //$(".datetimepicker").datetimepicker();
                $("#datetimepicker").mask("<?php echo $this->mdl_mcb_data->setting('default_date_format_mask'); ?> 99:99");
                $(".datetimepicker").mask("<?php echo $this->mdl_mcb_data->setting('default_date_format_mask'); ?> 99:99");
                $("#timepicker").mask("99:99");
                $(".timepicker").mask("99:99");
        });
</script>

<?php //$this->load->view('dashboard/jquery_date_picker'); ?>

<div class="grid_10" id="content_wrapper">

	<div class="section_wrapper">

	<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>">
		<h3 class="title_black"><?php echo $this->lang->line('astreinte_id') . $astreinte_id; ?>
		<?php if ($astreinte_intervention_id) echo ' / '.$this->lang->line('astreinte_intervention_id') . $astreinte_intervention_id;?>
			<span style="font-size: 60%;">
				<input type="submit" name="btn_return_to_list" class="uibutton" style="float: right; margin-top: 10px; margin-right: 10px;" value="<?php echo $this->lang->line('return_to_list'); ?>" />
				<input type="submit" name="btn_return_to_astreinte" class="uibutton" style="float: right; margin-top: 10px; margin-right: 10px;" value="<?php echo $this->lang->line('return_to_astreinte').' #'.$astreinte_id; ?>" />
			</span>
		</h3>

		<?php $this->load->view('dashboard/system_messages'); ?>

		<div class="content toggle">
			<dl>
				<dt><label><?php echo $this->lang->line('ticket_id');?>: </label></dt>
				<dd><input id="ticket_id" type="text" name="ticket_id" value="<?php echo $this->mdl_astreintes_interventions->form_value('ticket_id');?>" /></dd>
			</dl>

			<dl>
			<dt><label><?php echo $this->lang->line('start_date_time');?>: </label></dt>
			<dd><input class="datetimepicker" type="text" name="start_date_time" value="<?php if ($this->mdl_astreintes_interventions->form_value('start_date_time')) echo strftime('%d/%m/%Y %H:%M', $this->mdl_astreintes_interventions->form_value('start_date_time'));?>" /></dd>
			</dl>

			<dl>
			<dt><label><?php echo $this->lang->line('duration');?>: </label></dt>
			<dd><input class="timepicker" type="text" name="duration" value="<?php echo $this->mdl_astreintes_interventions->form_value('duration');?>" /></dd>
			</dl>

			<input type="submit" id="btn_submit" name="btn_submit" value="<?php echo $this->lang->line('submit');?>" />
			<input type="submit" id="btn_cancel" name="btn_cancel" value="<?php echo $this->lang->line('cancel');?>" />

		</div>
		</form>
	</div>
</div>



<?php $this->load->view('dashboard/footer'); ?>
