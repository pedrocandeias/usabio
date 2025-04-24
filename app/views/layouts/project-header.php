<?php
if (!isset($projectBase) && isset($this) && property_exists($this, 'projectBase')) {
    $projectBase = $this->projectBase;
    $projectTests = $this->projectTests;
    $projectParticipants = $this->projectParticipants;
    $projectAssignedUsers = $this->projectAssignedUsers;
}
?>

<!--begin::Navbar-->
<div class="card mb-6 mb-xl-9">
    <div class="card-body pt-9 pb-0">
        <!--begin::Details-->
        <div class="d-flex flex-wrap flex-sm-nowrap mb-6">
            <!--begin::Image-->
            <div class="d-flex flex-center flex-shrink-0 bg-light rounded w-100px h-100px w-lg-150px h-lg-150px me-7 mb-4">
                <?php if ( !empty( $projectBase['image'] ) ) : ?>
                <img class="mw-50px mw-lg-75px" src="<?php echo htmlspecialchars($projectBase['image']); ?>" alt="image" />
                <?php else : ?>
                <img class="mw-50px mw-lg-75px" src="assets/media/svg/brand-logos/plurk.svg" alt="image" />
                <?php endif; ?>
            </div>
            <!--end::Image-->
            <!--begin::Wrapper-->
            <div class="flex-grow-1">
                <!--begin::Head-->
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                    <!--begin::Details-->
                    <div class="d-flex flex-column">
                        <!--begin::Status-->
                        <div class="row">
                            <div class="col-md-9">
                                <div class="d-flex align-items-center mb-1">
                                    <h1 class="text-gray-800 text-hover-primary fs-2 fw-bold me-3"><?php echo $projectBase['title']; ?></h1>
                                    <?php if (!empty($projectBase['status'])) : ?>
                                        <?php if ($projectBase['status'] == 'completed') : ?>
                                        <span class="badge badge-light-success fw-bold me-auto px-4 py-3">Completed</span>
                                        <?php elseif ($projectBase['status'] == 'in_progress') : ?>
                                        <span class="badge badge-light-warning fw-bold me-auto px-4 py-3">In Progress</span>
                                        <?php else : ?>
                                        <span class="badge fw-bold me-auto px-4 py-3"></span>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <span class="badge badge-light-secondary fw-bold me-auto px-4 py-3">No Status</span>
                                    <?php endif; ?>
                                </div>
                       
                                <div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-gray-500">
                                    <?php echo nl2br(htmlspecialchars($projectBase['description'])); ?>
                                </div>
                            </div>
                            <div class="col-md-3 ">
                                <div class="d-flex  flex-column mb-4">
                                    <a href="/index.php?controller=Project&action=analysis&id=<?php echo $projectBase['id']; ?>" class="btn btn-lg btn-primary mb-3">
                                        <i class="ki-duotone ki-chart-simple-2 fs-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        Project Analysis
                                    </a>
                                    <a class="btn btn-lg btn-secondary" href="/index.php?controller=Import&action=uploadForm&project_id=<?php echo $projectBase['id']; ?>">
                                    <i class="ki-duotone ki-printer fs-1">
 <span class="path1"></span>
 <span class="path2"></span>
 <span class="path3"></span>
 <span class="path4"></span>
 <span class="path5"></span>
</i>   
                                    Print Project
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Details-->
                </div>
                <!--end::Head-->
                <!--begin::Info-->
                <div class="d-flex flex-wrap justify-content-start">
                    <!--begin::Stats-->
                    <div class="d-flex flex-wrap">
                        <!--begin::Stat-->
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="fw-semibold fs-6 text-gray-500">Created</div>
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold"><?php echo date('j F Y', strtotime($projectBase['created_at'])); ?></div>
                            </div>
                        </div>
                        <!--end::Stat-->
                            <!--begin::Stat-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="fw-semibold fs-6 text-gray-500">Updated</div>
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold"><?php echo date('j F Y', strtotime($projectBase['updated_at'])); ?></div>
                            </div>
                        </div>
                        <!--end::Stat-->
                        <?php if(!empty($projectTests)) : ?>
                        <!--begin::Stat-->
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="fw-semibold fs-6 text-gray-500">Tests</div>
                            <div class="fs-4 fw-bold" data-kt-countup="true" data-kt-countup-value="<?php echo count($projectTests); ?>">0</div>
                        </div>
                        <!--end::Stat-->
                        <?php endif; ?>
                        <?php if(!empty($projectParticipants)) : ?>
                        <!--begin::Stat-->
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="fw-semibold fs-6 text-gray-500">Participants</div>
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold" data-kt-countup="true" data-kt-countup-value="<?php echo count($projectParticipants); ?>">0</div>
                            </div>
                        </div>
                        <!--end::Stat-->
                        <?php endif; ?>
                        <?php if(!empty($projectAssignedUsers)) : ?>
                        <!--begin::Stat-->
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="fw-semibold fs-6 text-gray-500">Moderators</div>
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold" data-kt-countup="true" data-kt-countup-value="<?php echo count($projectAssignedUsers); ?>">
                                    <?php echo count($projectAssignedUsers); ?>
                                </div>      
                            </div>
                        </div>
                        <!--end::Stat-->
                        <?php endif; ?>
                    </div>
                    <!--end::Stats-->
                </div>
                <!--end::Info-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Details-->
        <div class="separator"></div>
        <!--begin::Nav-->
        <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
            <li class="nav-item">
                <a class="nav-link text-active-primary py-5 me-6 <?php if( $menuActive == 'overview') { echo 'active'; } ?>" href="/index.php?controller=Project&action=show&id=<?php echo $projectBase['id']; ?>">Overview</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-active-primary py-5 me-6 <?php if( $menuActive == 'tests') { echo 'active'; } ?>"  href="/index.php?controller=Test&action=index&project_id=<?php echo $projectBase['id']; ?>">Tests</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-active-primary py-5 me-6 <?php if( $menuActive == 'participants') { echo 'active'; } ?>" href="apps/projects/budget.html">Participants</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-active-primary py-5 me-6 <?php if( $menuActive == 'moderators') { echo 'active'; } ?>" href="apps/projects/users.html">Moderators</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-active-primary py-5 me-6 <?php if( $menuActive == 'import') { echo 'active'; } ?>" href="/index.php?controller=Import&action=uploadForm&project_id=<?php echo $projectBase['id']; ?>">Import</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-active-primary py-5 me-6 <?php if( $menuActive == 'export') { echo 'active'; } ?>" href="/index.php?controller=Export&action=index&project_id=<?php echo $projectBase['id']; ?>">Export</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-active-primary py-5 me-6 <?php if( $menuActive == 'settings') { echo 'active'; } ?>" href="/index.php?controller=Project&action=edit&id=<?php echo $projectBase['id']; ?>">Settings</a>
            </li>
        </ul>
        <!--end::Nav-->
    </div>
</div>
<!--end::Navbar-->
