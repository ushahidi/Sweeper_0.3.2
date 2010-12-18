<div id="rate">
	<div class="icon icon_<?php echo $_GET['type']; ?>"><!--icon--></div><!-- changed -->
	<div class="form">
		<div class="alert" style="display:none;">
			<ul></ul>
		</div>
		<?php if ($_GET['type'] == 'accurate') : ?>
			<h3>I can confirm this report is factually correct</h3>
		<?php elseif ($_GET['type'] == 'inaccurate') : ?>
			<h3>Please only confirm rating if content is misleading, false or untrustworthy:</h3>
				<div class="form-row">
					<input type="radio" value="falsehood" name="options" class="radio" />
					<h4>Falsehood</h4><p>Content is not based on reality</p>
				</div>
				<div class="form-row">
					<input type="radio" value="inaccurate" name="options" class="radio" />
					<h4>Inaccurate</h4><p>Content is factually incorrect</p>
				</div>
		<?php elseif ($_GET['type'] == 'crosstalk') : ?>
			<h3>This content is crosstalk (irrelevent)</h3>
		<?php else : ?>
			<h3>This content is spam</h3>
		<?php endif; ?>
			<div class="form-row">
				<button type="submit" onclick="RateContent('<?php echo $_GET['type']; ?>',  '<?php echo $_GET['id']; ?>')" class="submit"><span>Confirm</span></button>
				<button type="submit" onclick="Shadowbox.close()" class="cancel"><span>Cancel</span></button>
			</div>
	</div>
</div>
<script type="text/javascript" language="javascript">
    function RateContent(type, id) {
		$("div#rate div.alert").slideUp("slow");
        $("div#rate div.alert ul").children().remove();
		if(type == 'accurate') {
			Shadowbox.close();
			listController.MarkContentAsAccurate(id);
		} else if (type == 'inaccurate') {
			radio = false;
			var radio = $("input:radio[name='options']:checked").val();
			if (!radio) {
			    $("div#rate div.alert ul").append("<li>You must select an option before confiming</li>");
		        $("div#rate div.alert").slideDown();
			    return;
			} else if(radio == 'falsehood') {
				Shadowbox.close();
				listController.MarkContentAsInaccurate(id);
			} else if(radio == 'inaccurate') {
				Shadowbox.close();
				listController.MarkContentAsInaccurate(id);
			} else {
			    $("div#rate div.alert ul").append("<li>Something went wrong</li>");
		        $("div#rate div.alert").slideDown();
			}
		} else if (type == 'crosstalk') {
			Shadowbox.close();
			listController.MarkContentAsCrossTalk(id);
		} else if (type == 'spam') {
			Shadowbox.close();
			listController.MarkContentAsSpam(id);
		}
    }
</script>