<div class="wrap gabfire-plugin-settings">

	<?php require('header.php'); ?>

	<div class="metabox-holder has-right-sidebar">

		<?php require('sidebar.php'); ?>

		<div id="post-body">
			<div id="post-body-content">

				<?php if (!isset($_GET['type'])) { ?>

					<div id="icon-edit" class="icon32 icon32-posts-post"><br/></div>

					<h2><?php _e('Status',self::$text_domain); ?><a class="add-new-h2" href="<?php echo wp_nonce_url($_SERVER['PHP_SELF'] . '?page=' . self::$settings_page . '&type=add_edit&action=add', self::$prefix . 'add'); ?>"><?php _e('Add New', self::$text_domain); ?></a></h2>

					<table class="wp-list-table widefat fixed posts">
						<thead>
							<tr>
								<th><?php _e('id', self::$text_domain); ?></th>
								<th><?php _e('Label', self::$text_domain); ?></th>
								<th><?php _e('Label Count', self::$text_domain); ?></th>
								<th><?php _e('Public', self::$text_domain); ?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th><?php _e('id', self::$text_domain); ?></th>
								<th><?php _e('Label', self::$text_domain); ?></th>
								<th><?php _e('Label Count', self::$text_domain); ?></th>
								<th><?php _e('Public', self::$text_domain); ?></th>
							</tr>
						</tfoot>
						<tbody id="the-list">

						<?php
						$settings = get_option(self::$prefix . 'settings');

						/* Default values */

						if ($settings === false) {
							$settings = self::$default;
						}

						if (is_array($settings)) {

							/* Loop through all status */

							foreach ($settings as $status => $item) {

								?>
								<tr>

									<!-- ID -->

									<td>
										<strong><a href="<?php echo wp_nonce_url($_SERVER['PHP_SELF'] . '?page=' . self::$settings_page . '&type=add_edit&status=' . $status . '&action=edit', self::$prefix . 'edit'); ?>"><?php _e($status,self::$text_domain); ?></a></strong>
										<div class="row-actions">
											<span class="edit">
												<a href="<?php echo wp_nonce_url($_SERVER['PHP_SELF'] . '?page=' . self::$settings_page . '&type=add_edit&status=' . $status . '&action=edit', self::$prefix . 'edit'); ?>"><?php echo __('Edit', self::$text_domain); ?></a> |
											</span>

											<span class="delete">
												<span style="color:red;" class="<?php echo self::$prefix . 'delete'; ?>" id="<?php echo self::$prefix . 'delete_' . $status; ?>"><?php _e('Delete', self::$text_domain); ?></span>

												<span id="<?php echo self::$prefix . 'delete_url_' . $status; ?>" style="display:none;"><?php echo wp_nonce_url($_SERVER['PHP_SELF'] . '?page=' . self::$settings_page . '&type=dashboard&status=' . $status . '&action=delete', self::$prefix . 'delete'); ?></span>
											</span>
										</div>
									</td>

									<!-- Label -->

									<td>
										<span><?php echo isset($item['label']) ? $item['label'] : ''; ?></span>
									</td>

									<!-- Label Count -->

									<td>
										<span><?php echo isset($item['label_count']) ? $item['label_count'] : ''; ?></span>
									</td>

									<!-- Public -->

									<td>
										<span><?php echo isset($item['public']) && $item['public'] ? 'yes' : 'no'; ?></span>
									</td>
								</tr>
								<?php
							}
						}
					?>
						</tbody>
					</table>
				<?php } ?>

				<?php if (isset($_GET['type']) && $_GET['type'] == 'add_edit') { ?>

				<h1><?php _e('Settings', self::$text_domain); ?></h1>

				<form method="post" class="<?php echo self::$prefix; ?>form">

					<table class="form-table">
						<tbody>

							<!-- Status -->

							<tr>
								<th>
									<label><?php _e('Status', self::$text_domain); ?></label>
									<td>
										<input id="<?php echo self::$prefix; ?>status" name="<?php echo self::$prefix; ?>status" type="text" size="50" value="<?php echo ((isset($_GET['action']) && $_GET['action'] == "edit" && (isset($_GET['status']) && $_GET['status'] != '')) ? $_GET['status'] : ''); ?>"  <?php echo isset($_GET['action']) && $_GET['action'] == 'edit' ? 'readonly' : ''; ?>><br/>
										<em><?php _e('The id of the status, this should be unique.', self::$text_domain); ?></em>
									</td>
								</th>
							</tr>

							<!-- Label -->

							<tr>
								<th>
									<label><?php _e('Label', self::$text_domain); ?></label>
									<td>
										<input id="<?php echo self::$prefix; ?>label" name="<?php echo self::$prefix; ?>label" type="text" size="50" value="<?php echo isset($data['label']) ? esc_attr($data['label']) : ''; ?>"><br/>
										<em><?php _e('A descriptive name for the post status.', self::$text_domain); ?></em>
									</td>
								</th>
							</tr>

							<!-- Public -->

							<tr>
								<th>
									<label><?php _e('Public', self::$text_domain); ?></label>
									<td>
										<input id="<?php echo self::$prefix; ?>public" name="<?php echo self::$prefix; ?>public" type="checkbox" <?php echo isset($data['public']) && $data['public'] ? 'checked="checked"' : ''; ?>><br/>
										<em><?php _e('Whether posts of this status should be shown in the front end of the site.', self::$text_domain); ?></em>
									</td>
								</th>
							</tr>

							<!-- Exclude from Search -->

							<tr>
								<th>
									<label><?php _e('Exclude from Search', self::$text_domain); ?></label>
									<td>
										<input id="<?php echo self::$prefix; ?>exclude-from-search" name="<?php echo self::$prefix; ?>exclude-from-search" type="checkbox" <?php echo isset($data['exclude_from_search']) && $data['exclude_from_search'] ? 'checked="checked"' : ''; ?>><br/>
										<em><?php _e('Whether to exclude posts with this post status from search results.', self::$text_domain); ?></em>
									</td>
								</th>
							</tr>

							<!-- Show in Admin All List -->

							<tr>
								<th>
									<label><?php _e('Show in Admin All List', self::$text_domain); ?></label>
									<td>
										<input id="<?php echo self::$prefix; ?>show-in-admin-all-list" name="<?php echo self::$prefix; ?>show-in-admin-all-list" type="checkbox" <?php echo isset($data['show_in_admin_all_list']) && $data['show_in_admin_all_list'] ? 'checked="checked"' : ''; ?>><br/>
										<em><?php _e('Whether to include posts in the edit listing for their post type.', self::$text_domain); ?></em>
									</td>
								</th>
							</tr>

							<!-- Show in Admin Status List -->

							<tr>
								<th>
									<label><?php _e('Show in Admin Status List', self::$text_domain); ?></label>
									<td>
										<input id="<?php echo self::$prefix; ?>show-in-admin-status-list" name="<?php echo self::$prefix; ?>show-in-admin-status-list" type="checkbox" <?php echo isset($data['show_in_admin_status_list']) && $data['show_in_admin_status_list'] ? 'checked="checked"' : ''; ?>><br/>
										<em><?php _e('Show in the list of statuses with post counts at the top of the edit listings, e.g. All (12) , Published (9) , My Custom Status (2) ...', self::$text_domain); ?></em>
									</td>
								</th>
							</tr>

							<!-- Label Count -->

							<tr>
								<th>
									<label><?php _e('Label Count', self::$text_domain); ?></label>
									<td>
										<input id="<?php echo self::$prefix; ?>label-count" name="<?php echo self::$prefix; ?>label-count" type="text" size="50" value="<?php echo isset($data['label_count']) ? esc_attr($data['label_count']) : ''; ?>"><br/>
										<em><?php _e("The text to display on the admin screen (or you won't see your status count).", self::$text_domain); ?></em>
									</td>
								</th>
							</tr>

						</tbody>
					</table>

					<?php wp_nonce_field(self::$prefix . 'admin_settings'); ?>

					<?php submit_button(); ?>

				</form>
				<?php } ?>

<?php require('footer.php'); ?>