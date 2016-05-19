<?php
/**
 * @file
 * template.php
 */

/**
 * THEME_preprocess_page().
 */
function valhalla_bs_preprocess_page(&$vars) {
  $vars['valghalla_deltagere'] = valghalla_bs_theme_navbarmenu('valghalla/deltagere');
  $vars['valghalla_administration'] = valghalla_bs_theme_navbarmenu('valghalla/administration');
  $vars['valghalla_lists'] = valghalla_bs_theme_navbarmenu('valghalla_lists');
  $vars['admin_valghalla'] = valghalla_bs_theme_navbarmenu('admin/valghalla');
}

/**
 * Genereate navbar menu.
 */
function valghalla_bs_theme_navbarmenu($path) {
  $parent = menu_link_get_preferred($path);
  $parameters = array(
    'active_trail' => array($parent['plid']),
    'only_active_trail' => FALSE,
    'min_depth' => $parent['depth'] + 1,
    'max_depth' => $parent['depth'] + 1,
    'conditions' => array('plid' => $parent['mlid']),
  );

  $children = menu_build_tree($parent['menu_name'], $parameters);

  $tree_output = menu_tree_output($children);

  $items = array();

  foreach ($tree_output as $item_id => $item_data) {
    if (is_numeric($item_id) && is_array($item_data)) {
      $items[] = l($item_data['#title'], $item_data['#href'], array(
          'attributes'    => $item_data['#attributes'],
          'html'      => TRUE,
        )
      );
    }
  }

  $menu = theme('item_list', array(
    'items' => $items,
    'type' => 'ul',
    'attributes' => array(
      'class' => 'dropdown-menu',
    ),
  ));

  $toggle = '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' . $parent['link_title'] . ' <b class="caret"></b></a>';
  if ($menu) {
    return $toggle . $menu;
  }
}

/**
 * Last revision editor.
 */
function valhalla_bs_last_editor($node) {
  if (isset($node->revision_uid)) {
    $uid = $node->revision_uid;
  }
  else {
    $uid = $node->uid;
  }

  $account = user_load($uid);

  if (user_access('access user profiles')) {
    return l($account->name, 'user/' . $account->uid);
  }
  return $account->name;
}

function valhalla_bs_vol_mail($node) {
  if ($field = field_get_items('node', $node, 'field_email')) {
    return l($field[0]['email'], 'mailto:' . $field[0]['email']);
  }
  return '';
}

function valhalla_bs_vol_address($node) {
  $address = '';

  if ($field = field_get_items('node', $node, 'field_address_road')) {
    $address .= $field[0]['value'] . ' ';
  }

  if ($field = field_get_items('node', $node, 'field_address_road_no')) {
    $address .= $field[0]['value'] . ', ';
  }

  if ($field = field_get_items('node', $node, 'field_address_floor')) {
    $address .= $field[0]['value'] . ' ';
  }

  if ($field = field_get_items('node', $node, 'field_address_door')) {
    $address .= $field[0]['value'] . ', ';
  }

  if ($field = field_get_items('node', $node, 'field_address_zipcode')) {
    $address .= $field[0]['value'] . ' ';
  }

  if ($field = field_get_items('node', $node, 'field_address_city')) {
    $address .= $field[0]['value'];
  }

  return $address;
}

function valhalla_bs_vol_phone($node) {
  if ($field = field_get_items('node', $node, 'field_phone')) {
    return $field[0]['value'];
  }
  return '';
}

function valhalla_bs_vol_no_mail($node) {
  if ($field = field_get_items('node', $node, 'field_no_mail')) {
    if ($field[0]['value']) {
      return '<b>Deltageren er fritaget for digital post</b>';
    }
  }
  return '';
}

function valhalla_bs_vol_cpr($node) {
  if ($field = field_get_items('node', $node, 'field_cpr_number')) {
    $cpr = $field[0]['value'];
  }

  $age = _valghalla_helper_get_age_from_cpr($cpr);
  $age = ' (' . $age . ' år)';

  if (user_access('see all psn numbers')) {
    return $cpr . $age;
  }

  return substr($cpr, 0, 6) . $age;
}

function valhalla_bs_vol_election_info($node) {

  $output = '';

  if ($field = field_get_items('node', $node, 'field_electioninfo')) {

    foreach ($field as $data) {
      $fc = field_collection_item_load($data['value']);

      // Election.
      if ($field = field_get_items('field_collection_item', $fc, 'field_election')) {
        if ($field[0]['entity']) {
          $_node = $field[0]['entity'];
          $election = $_node->title;
        }
      }

      // Polling station.
      if ($field = field_get_items('field_collection_item', $fc, 'field_vlnt_station')) {
        if ($field[0]['entity']) {
          $_node = $field[0]['entity'];
          $polling_station = l($_node->title, 'volunteers/station/' . $_node->nid);
        }
      }

      // Post role.
      if ($field = field_get_items('field_collection_item', $fc, 'field_post_role')) {
        if ($field[0]['entity']) {
          $_node = $field[0]['entity'];
          $role_title = $_node->title;

          if ($field = field_get_items('node', $_node, 'field_description')) {
            $role_description = $field[0]['value'];
          }
        }
      }

      // Party.
      if ($field = field_get_items('field_collection_item', $fc, 'field_post_party')) {
        if ($field[0]['entity']) {
          $_term = $field[0]['entity'];
          $party = $_term->name;
        }
      }

      // Status.
      $rsvp = '';
      if ($field = field_get_items('field_collection_item', $fc, 'field_rsvp')) {
        $rsvp_map = array(
          0 => 'Ikke svaret',
          1 => 'Ja',
          2 => 'Nej',
          3 => 'Aldrig',
        );
        $rsvp = $rsvp_map[$field[0]['value']];
      }

      $rsvp_comment = '';
      if ($field = field_get_items('field_collection_item', $fc, 'field_rsvp_comment')) {
        $rsvp_comment = $field[0]['value'];
      }
      if ($field = field_get_items('field_collection_item', $fc, 'field_token')) {
        $rsvp_link = l(t('(skift status)'), 'volunteers/rsvp/' . $field[0]['value']);
      }

      $output .= '
        <h3>' . $election . '</h3>
        <table class="table">
          </tr>
            <td>Valgsted:</td>
            <td>' . $polling_station . '</td>
          </tr>
          </tr>
            <td>Rolle:</td>
            <td>' . $role_title . ' / ' . $role_description . '</td>
          </tr>
          </tr>
            <td>Parti:</td>
            <td>' . $party . '</td>
          </tr>
          </tr>
            <td>Status:</td>
            <td>' . $rsvp . ' ' . $rsvp_link . '</td>
          </tr>
          </tr>
            <td>Status kommentar:</td>
            <td><i>' . $rsvp_comment . '</i></td>
          </tr>
        </table>
        <br /><br />
        ';
    }
  }

  return $output;
}
