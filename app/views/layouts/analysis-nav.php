 <!--begin::Analytics navigation-->
<div class="card">
    <div class="card-body">
        <ul class="nav mx-auto flex-shrink-0 flex-center flex-wrap border-transparent fs-6 fw-bold">
            <li class="nav-item my-3">
                <a class="btn btn-active-light-primary fw-bolder nav-link btn-color-gray-700 px-3 px-lg-8 mx-1 text-uppercase <?php echo ($activeTab === 'overview') ? 'active' : ''; ?>" href="/index.php?controller=Analysis&action=index&id=<?php echo $project['id']; ?>">📊 Overview</a>
            </li>
            <li class="nav-item my-3">
                <a class="btn btn-active-light-primary fw-bolder nav-link btn-color-gray-700 px-3 px-lg-8 mx-1 text-uppercase <?php echo ($activeTab === 'tasks') ? 'active' : ''; ?>" href="/index.php?controller=Analysis&action=tasks&id=<?php echo $project['id']; ?>">📋 Task Success</a>
            </li>
            <li class="nav-item my-3">
                <a class="btn btn-active-light-primary fw-bolder nav-link btn-color-gray-700 px-3 px-lg-8 mx-1 text-uppercase <?php echo ($activeTab === 'questionnaires') ? 'active' : ''; ?>" href="/index.php?controller=Analysis&action=questionnaires&id=<?php echo $project['id']; ?>">📑 Questionnaires</a>
            </li>
            <li class="nav-item my-3">
                <a class="btn btn-active-light-primary fw-bolder nav-link btn-color-gray-700 px-3 px-lg-8 mx-1 text-uppercase <?php echo ($activeTab === 'sus') ? 'active' : ''; ?>" href="/index.php?controller=Analysis&action=sus&id=<?php echo $project['id']; ?>">🧠 SUS</a>
            </li>
            <li class="nav-item my-3">
                <a class="btn btn-active-light-primary fw-bolder nav-link btn-color-gray-700 px-3 px-lg-8 mx-1 text-uppercase <?php echo ($activeTab === 'participants') ? 'active' : ''; ?>" href="/index.php?controller=Analysis&action=participants&id=<?php echo $project['id']; ?>">👥 Participants</a>
            </li>
        
            <li class="nav-item my-3">
                <a class="btn btn-active-light-primary fw-bolder nav-link btn-color-gray-700 px-3 px-lg-8 mx-1 text-uppercase <?php echo ($activeTab === 'participants') ? 'active' : ''; ?>" href="/index.php?controller=Export&action=exportProjectPdf&project_id=<?php echo $project['id']; ?>">📦 Export Report</a>
            </li>
        </ul>
    </div>
</div>
<!--end::Analytics navigation-->