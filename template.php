<?php

/**
 * @file
 * template.php
 */

function valhalla_bs_preprocess_page(&$vars){
  $vars['valghalla_deltagere'] = valghalla_bs_theme_navbarmenu('valghalla/deltagere');
  $vars['valghalla_administration'] = valghalla_bs_theme_navbarmenu('valghalla/administration');
  $vars['valghalla_lists'] = valghalla_bs_theme_navbarmenu('valghalla_lists');
  $vars['admin_valghalla'] = valghalla_bs_theme_navbarmenu('admin/valghalla');
}

function valghalla_bs_theme_navbarmenu($path){
  $parent = menu_link_get_preferred($path);
  $parameters = array(
      'active_trail' => array($parent['plid']),
      'only_active_trail' => FALSE,
      'min_depth' => $parent['depth']+1,
      'max_depth' => $parent['depth']+1,
      'conditions' => array('plid' => $parent['mlid']),
    );
  $children = menu_build_tree($parent['menu_name'], $parameters);

  $tree_output = menu_tree_output($children);

  foreach($tree_output as $item_id => $item_data) {
    if(is_numeric($item_id) && is_array($item_data)) {
      $items[] = l($item_data['#title'], $item_data['#href'], array(
          'attributes'    => $item_data['#attributes'],
          'html'      => TRUE,
        )
      );
    }
  }

  $menu = theme('item_list', array('items' => $items, 'type' => 'ul', 'attributes' => array('class' => 'dropdown-menu')));

  $toggle = '<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$parent['link_title'].' <b class="caret"></b></a>';
  if($menu){
    return $toggle . $menu;
  }
}
