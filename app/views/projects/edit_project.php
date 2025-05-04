<!-- app/views/projects/form.php -->

<?php

$menuActive = 'settings';
$pageTitle = 'Project Settings';
$pageDescription = 'Manage your projects settings.';
$title = 'Project settings';
$headerNavbuttons = [
    __('back_to_projects_list') => [
        'url' => '/index.php?controller=Project&action=index',
        'icon' => 'ki-duotone ki-home fs-2',
        'class' => 'btn btn-custom btn-flex btn-color-white btn-active-light',
        'id' => 'kt_back_home_primary_button',
    ],
];                        

require __DIR__ . '/../layouts/header.php'; ?>

<!--begin::Container-->
<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
    <!--begin::Post-->
    <div class="content flex-row-fluid" id="kt_content">
        <?php require_once __DIR__ . '/../layouts/project-header.php'; ?>
       
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label"><?php echo __('project_details'); ?></h3>
                </div>
                <div class="card-toolbar">

                <button type="button" class="btn btn-sm btn-icon btn-color-light-dark btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                    <i class="ki-duotone ki-element-plus fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                    </i>
                                </button>
                        <!--begin::Menu 3-->
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3" data-kt-menu="true">
                            <!--begin::Heading-->
                            <div class="menu-item px-3">
                                <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase"><?php echo __('settings'); ?></div>
                            </div>
                            <!--end::Heading-->
                            <div class="separator my-2"></div>
                            <!--end:Menu item-->
                            <!--begin::Menu item-->
             
                           
                            <div class="menu-item px-3">
                                <a href="/index.php?controller=Duplicate&action=selectProject" class="menu-link bg-outline-info px-3"><?php echo __('duplicate_project'); ?></a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="/index.php?controller=Project&action=destroy&id=<?php echo $project['id']; ?>" class="menu-link bg-danger text-white px-3" onclick="return confirm('<?php echo __('are_you_sure_you_want_to_delete_this_project?');?>');"><?php echo __('delete_project'); ?></a>
                            </div>         
                            <!--end::Menu item-->
                        </div>
                        <!--end::Menu 3-->                       

                </div>
            </div>
            <div class="card-body">
                <h5 class="card-title"><?php echo __('edit_project'); ?></h5>
                    <form method="POST" action="/index.php?controller=Project&action=<?php echo $project['id'] ? 'update' : 'store'; ?>">
                        <?php if ($project['id']): ?>
                            <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                        <?php endif; ?>


                        <div class="row g-5 g-xl-8">

                            <div class="col-xl-12">
                                <div class="card  mb-xl-8 shadow-sm">
                                    <div class="card-header py-5 bg-primary">
                                        <h3 class="card-title">
                                            <span class="card-label fw-bold fs-3 mb-1 text-white"><?php echo __('project_title'); ?></span>
                                        </h3>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <div class="fw-semibold fs-6">
                                            <label for="title" class="form-label"><?php echo __('project_title'); ?></label>
                                            <textarea class="form-control" id="title" name="title" rows="3" required><?php echo htmlspecialchars($project['title']); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                             </div>
                            <!--begin::Col (Product under test)-->
                            <div class="col-xl-4">
                                <div class="card  mb-xl-8 shadow-sm">
                                    <div class="card-header py-5 bg-primary">
                                        <h3 class="card-title">
                                            <span class="card-label fw-bold fs-3 mb-1 text-white">Produto em Teste</span>
                                        </h3>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <div class="fw-semibold fs-6">
                                            Guiador Adaptado para Bicicletas de Uso Público
                                        </div>
                                    </div>
                                </div>
                                <div class="card mb-xl-8 shadow-sm">
                                    <div class="card-header bg-primary">
                                        <h3 class="card-title">
                                            <span class="card-label fw-bold fs-3 text-white">Moderadores definidos</span>
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                                                    <p class="text-muted">Nenhum moderador atribuído a este projecto.</p>
                                                            </div>
                                </div>
                                
                            </div>
                            <!--end::Col-->

                            <!--begin::Col (Business case + Test objectives)-->
                            <div class="col-xl-8">
                                <div class="row gx-5 gx-xl-8 mb-5">

                                    <div class="col-xl-4 mb-5 mb-xl-0">
                                        <div class="card card-xl-stretch mb-xl-3 shadow-sm">
                                            <div class="card-header bg-primary">
                                                <h3 class="card-title">
                                                    <span class="card-label fw-bold fs-3 text-white">Objetivos do Teste</span>
                                                </h3>
                                            </div>
                                            <div class="card-body d-flex flex-column">
                                                <div class="fw-semibold fs-6">
                                                    Avaliar a estabilidade, o nível de esforço necessário para manter o equilíbrio, a precisão no controlo direcional, bem como o conforto ergonómico do ponto de contacto com a mão ou braço ativo.                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-8">
                                        <div class="card card-xl-stretch shadow-sm">
                                            <div class="card-header bg-primary">
                                                <h3 class="card-title">
                                                    <span class="card-label fw-bold fs-3 text-white">Caso de Negócio</span>
                                                </h3>
                                            </div>
                                            <div class="card-body d-flex flex-column">
                                                <div class="fw-semibold fs-6">
                                                    Garantir acessibilidade, segurança e conforto para todos os perfis de utilizadores.                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="row gx-5 gx-xl-8 mb-5 mb-xl-8">
                                    <!-- Participants and Location/Dates -->
                                    <div class="col-xl-6 mb-xl-8">
                                        <div class="card card-xl-stretch mb-xl-8 shadow-sm">
                                            <div class="card-header bg-primary">
                                                <h3 class="card-title">
                                                    <span class="card-label fw-bold fs-3 text-white">Participantes</span>
                                                </h3>
                                            </div>
                                            <div class="card-body d-flex flex-column">
                                                <div class="fw-semibold fs-6">
                                                    Utilizadores de bicicletas de uso público com mobilidade reduzida num dos membros superiores.                                </div>
                                            </div>
                                        </div>

                                        <div class="card card-xl-stretch mb-1 shadow-sm">
                                            <div class="card-header bg-primary">
                                                <h3 class="card-title">
                                                    <span class="card-label fw-bold fs-3 text-white">Local e Datas</span>
                                                </h3>
                                            </div>
                                            <div class="card-body d-flex flex-column">
                                                <div class="fw-semibold fs-6">
                                                    O teste será realizado em um local seguro e controlado, em datas a serem definidas.                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Responsibilities and Equipment -->
                                    <div class="col-xl-6">
                                        <div class="card card-xl-stretch mb-xl-8 shadow-sm">
                                            <div class="card-header bg-primary">
                                                <h3 class="card-title">
                                                    <span class="card-label fw-bold fs-3 text-white">Responsabilidades</span>
                                                </h3>
                                            </div>
                                            <div class="card-body d-flex flex-column">
                                                <div class="fw-semibold fs-6">
                                                    Os participantes serão responsáveis por fornecer feedback honesto e preciso sobre a sua experiência ao utilizar o guiador adaptado.                                </div>
                                            </div>
                                        </div>

                                        <div class="card card-xl-stretch mb-1 shadow-sm">
                                            <div class="card-header bg-primary">
                                                <h3 class="card-title">
                                                    <span class="card-label fw-bold fs-3 text-white">Equipamento</span>
                                                </h3>
                                            </div>
                                            <div class="card-body d-flex flex-column">
                                                <div class="fw-semibold fs-6">
                                                    Bicicleta equipada com o guiador adaptado, percurso definido com obstáculos leves.                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!--end::Col-->
                        </div>



                        
                        <?php
                        $fields = [
                            'title' => 'Project Title',
                            'description' => 'Project Description',
                            'product_under_test' => 'Product Under Test',
                            'business_case' => 'Business Case',
                            'test_objectives' => 'Test Objectives',
                            'participants' => 'Participants',
                            'equipment' => 'Equipment',
                            'responsibilities' => 'Responsibilities',
                            'location_dates' => 'Location & Dates',
                            'test_procedure' => 'Procedure',
                        ];

                        foreach ($fields as $name => $label): ?>
                            <div class="mb-3">
                                <label for="<?php echo $name; ?>" class="form-label"><?php echo $label; ?></label>
                                <textarea class="form-control" id="<?php echo $name; ?>" name="<?php echo $name; ?>" rows="3" required><?php echo htmlspecialchars($project[$name]); ?></textarea>
                            </div>
                        <?php endforeach; ?>


                        <button type="submit" class="btn btn-primary">Update Project</button>
                        <a href="/index.php?controller=Project&action=index" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>

</body>
</html>

