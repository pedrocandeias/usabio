					
					<!--begin::Footer-->
					<?php if (empty($minimalLayout)) : ?>
					<footer>
						<div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
							<!--begin::Container-->
							<div class="container-xxl d-flex flex-column flex-md-row align-items-center justify-content-between">
								<!--begin::Copyright-->
								<div class="text-gray-900 order-2 order-md-1">
									<span class="text-muted fw-semibold me-1">&copy; <?php echo date('Y'); ?></span>
									<a href="https://testflow.design" target="_blank" class="text-gray-800 text-hover-primary">TestFlow UX</a>
								</div>
								<!--end::Copyright-->
								<!--begin::Menu-->
								<ul class="menu menu-gray-600 menu-hover-primary fw-semibold order-1">
									<li class="menu-item">
										<a href="about.html" target="_blank" class="menu-link px-2">About</a>
									</li>
									<li class="menu-item">
										<a href="help.html" target="_blank" class="menu-link px-2">Help</a>
									</li>
									<li class="menu-item">
										<a href="Privacy Policty" target="_blank" class="menu-link px-2">Privacy Policty</a>
									</li>
									<li class="menu-item">
										<a href="Terms and Conditions" target="_blank" class="menu-link px-2">Terms & Conditions</a>
									</li>
								</ul>
								<!--end::Menu-->
							</div>
							<!--end::Container-->
						</div>
						<!--end::Footer-->
					</footer>
					<?php else: ?>
					<footer class="d-flex flex-column flex-md-row align-items-center justify-content-between py-4 fixed-bottom">
						<div class="text-gray-900 order-2 order-md-1">
							<span class="text-muted fw-semibold me-1">&copy; <?php echo date('Y'); ?></span>
							<a href="https://testflow.design" target="_blank" class="text-gray-800 text-hover-primary">TestFlow UX</a>
						</div>
					<?php endif; ?>
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Page-->
		</div>
	 <!--end::Root-->
	</div>
    
  <!--begin::Scrolltop-->
  <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
    <i class="ki-duotone ki-arrow-up">
      <span class="path1"></span>
      <span class="path2"></span>
    </i>
  </div>
  <!--end::Scrolltop-->



<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="savedToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        <span id="toastMessage">Action complete</span>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>