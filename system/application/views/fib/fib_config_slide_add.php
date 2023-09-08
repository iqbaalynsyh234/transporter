<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/plugins/select2/select2-metronic.css"/>
<script src="<?php echo base_url();?>assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/plugins/select2/select2.min.js"></script>
<script>
jQuery.maxZIndex = jQuery.fn.maxZIndex = function(opt) {
				    var def = { inc: 10, group: "*" };
				    jQuery.extend(def, opt);
				    var zmax = 0;
				    jQuery(def.group).each(function() {
				        var cur = parseInt(jQuery(this).css('z-index'));
				        zmax = cur > zmax ? cur : zmax;
				    });
				    if (!this.jquery)
				        return zmax;
				
				    return this.each(function() {
				        zmax += def.inc;
				        jQuery(this).css("z-index", zmax);
				    });
				}
				
jQuery(document).ready(
		function()
		{
			$('select').select2();
			showclock();
			
		}
	);
	
	function frmadd_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>fibconfigslide/save", jQuery("#frmadd").serialize(),	
			function(r)
			{
				jQuery("#loader").hide();
				if (r.error)
				{
					alert(r.message);
					return false;
				}
				
				alert(r.message);
				location = r.redirect;
			}
			, "json"	
		);
		return false;
	}
</script>

<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
	<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<br />&nbsp;
		<h1>Add Config FIB Slide</h1>
		<form name="frmadd" id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
			<input type="hidden" name="fib_config_id" id="fib_config_id" value="<?php if(isset($data)){ echo $data->fib_config_id; } ?>"/>
			<input type="hidden" name="fib_config_app_id" id="fib_config_app_id" value="<?php if(isset($data)){ echo $data->fib_config_app_id; } ?>"/>
			<table width="100%" cellpadding="3" class="tablelist">
				<tr>
					<td>Config Name</td>
					<td>:</td>
					<td><input type="text" name="fib_config_name" id="fib_config_name"  size = "70" class="form-control"/></td>
				</tr>
				<tr>
					<td>Total Vehicle<small>Per Slide</small></td>
					<td>:</td>
					<td><input type="text" name="fib_config_vehicle" id="fib_config_vehicle"  size = "10" class="form-control"/></td>
				</tr>
				<tr>
					<td>Status</td>
					<td>:</td>
					<td> 
						<input name="fib_config_status" type="radio" value="0"> NO</input>
						<input name="fib_config_status" type="radio" value="1" checked > YES</input> <br />
					</td>
				</tr>
				<tr>
					<td>
						<input type="submit" name="btnsave" id="btnsave" value=" Save " />
						<button type="reset">Reset</reset>
						<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>