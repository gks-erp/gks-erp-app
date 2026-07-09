<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function select_gks_crm_tasks() {
  return "SELECT gks_crm_tasks.*, ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, 
  ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
  ".GKS_WP_TABLE_PREFIX."users_print.gks_nickname AS gks_nickname_print,
  ".GKS_WP_TABLE_PREFIX."users.gks_nickname,gks_users.pelati_sxolio,gks_users.order_sxolio,
  gks_crm_tasks_status.task_status_descr, gks_crm_tasks_status.task_status_color, gks_crm_tasks_status.task_status_sortorder,
  gks_company.company_title, gks_company_subs.company_sub_title,
  gks_country.country_name, gks_country.country_initials, gks_country.country_initials3,gks_country.country_ee,
  gks_nomoi.nomos_descr,
  ".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname AS gks_nickname_assigned, 
  gks_crm_channel_sale.crm_channel_sale_descr, 
  ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.gks_nickname as crm_channel_contact_gks_nickname,
  gks_ads_campain.ads_campain_name,
  gks_eshop_fiscal_position.fiscal_position_descr,
  gks_eshop_pricelist.pricelist_descr
  
  FROM (((((((((((((((gks_crm_tasks 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_crm_tasks.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_crm_tasks.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_tasks.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_print ON gks_crm_tasks.print_user_id = ".GKS_WP_TABLE_PREFIX."users_print.ID) 
  LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
  LEFT JOIN gks_crm_tasks_status ON gks_crm_tasks.task_status_id = gks_crm_tasks_status.id_crm_task_status)
  LEFT JOIN gks_company ON gks_crm_tasks.company_id = gks_company.id_company) 
  LEFT JOIN gks_company_subs ON gks_crm_tasks.company_sub_id = gks_company_subs.id_company_sub) 
  LEFT JOIN gks_country ON gks_crm_tasks.country_id = gks_country.id_country) 
  LEFT JOIN gks_nomoi ON gks_crm_tasks.nomos_id = gks_nomoi.id_nomos)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_assigned ON gks_crm_tasks.assigned_id = ".GKS_WP_TABLE_PREFIX."users_assigned.ID) 
  LEFT JOIN gks_crm_channel_sale ON gks_crm_tasks.crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact ON gks_crm_tasks.crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.ID)
  LEFT JOIN gks_ads_campain ON gks_crm_tasks.crm_channel_campain_id = gks_ads_campain.id_ads_campain)
  LEFT JOIN gks_eshop_fiscal_position ON gks_crm_tasks.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position)
  LEFT JOIN gks_eshop_pricelist ON gks_crm_tasks.pricelist_id = gks_eshop_pricelist.id_pricelist
  ";
}
