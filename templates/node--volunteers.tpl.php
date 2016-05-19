<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>
  <span class="pull-right">Gemt: <?php print valhalla_bs_last_editor($node) . ' ' . format_date($node->changed); ?></span>

  <br />
  <br />
  <table class="table">
    <tr>
      <td>Adresse:</td>
      <td><?php print valhalla_bs_vol_address($node);?></td>
    </tr>
    <tr>
      <td>Telefon:</td>
      <td><?php print valhalla_bs_vol_phone($node);?></td>
    </tr>
    <tr>
      <td>Email:</td>
      <td><?php print valhalla_bs_vol_mail($node);?></td>
    </tr>
    <tr>
      <td>Cpr:</td>
      <td><?php print valhalla_bs_vol_cpr($node);?></td>
    </tr>
    <tr>
      <td></td>
      <td><?php print valhalla_bs_vol_no_mail($node);?></td>
    </tr>
  </table>
  <br /><br />

  <?php print valhalla_bs_vol_election_info($node); ?>
</div>
