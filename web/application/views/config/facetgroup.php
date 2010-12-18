<?php /* if($groups != null) : foreach($groups as $name => $group) : */ ?>
<div id="sources">
	<div class="icon"><!--icon--></div><!-- changed -->
	<li class="facet-group">
		<div class="nav-container">
			<h4><?php /* echo($name); */ ?></h4>
			<div class="nav-inner clearfix">
				<ul class="facets">
					<?php /* foreach($group["facets"] as $facet) : */ ?>
						<li>
							<?php /*  if($group["selected"] == "true") : */ ?>
								<a href="javascript:listController.DeselectFacet('<?php /* echo($group["key"]); */ ?>');" title="Deselect <?php /* echo($facet['name']); */ ?>">
									<?php /* echo(Html::image("media/images/button-deactivate.png", array())); */ ?>
									<?php /* echo(strlen($facet["name"]) > $maxNameLength ? substr($facet["name"], 0, $maxNameLength) . " ..." : $facet["name"]) ; */ ?>
								</a>
							<?php /* else : */ ?>
								<a href="javascript:listController.SelectFacet('<?php /* echo($group["key"]); */ ?>', '<?php /* echo($facet["id"]); */ ?>');" title="Select <?php /* echo($facet['name']); */ ?>">
									<?php /* echo(Html::image("media/images/button-activate.png", array())); */ ?>
									<?php /* echo(strlen($facet["name"]) > $maxNameLength ? substr($facet["name"], 0, $maxNameLength) . " ..." : $facet["name"]) ; */ ?>
									<?php /* echo("(" . $facet["count"] . ")"); */ ?>
								</a>
							<?php /* endif; */ ?>
						</li>
					<?php /* endforeach; */ ?>
				</ul>
			</div>
		</div>
	</li>
</div>
<?php /* endforeach; endif; */ ?>
