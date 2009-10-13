<?php
/**
 * Update documents in a resource group
 *
 *
 * @package modx
 * @subpackage processors.security.documentgroup
 */
if (!$modx->hasPermission('access_permissions')) return $modx->error->failure($modx->lexicon('permission_denied'));
$modx->lexicon->load('resource','access');

/* format data */
$_POST['resource'] = substr(strrchr($_POST['resource'],'_'),1);
$_POST['resource_group'] = substr(strrchr($_POST['resource_group'],'_'),1);

if (empty($_POST['resource']) || empty($_POST['resource_group'])) return $modx->error->failure('Invalid data.');

/* get resource */
$resource = $modx->getObject('modResource',$_POST['resource']);
if ($resource == null) return $modx->error->failure($modx->lexicon('resource_err_nfs',array('id' => $_POST['resource'])));

/* get resource group */
$resourceGroup = $modx->getObject('modResourceGroup',$_POST['resource_group']);
if ($resourceGroup == null) return $modx->error->failure($modx->lexicon('resource_group_err_ns'));

/* check to make sure already isnt in group */
$alreadyExists = $modx->getObject('modResourceGroupResource',array(
	'document' => $resource->get('id'),
	'document_group' => $resourceGroup->get('id'),
));
if ($alreadyExists) return $modx->error->failure($modx->lexicon('resource_group_resource_err_ae'));

/* create resource group -> resource pairing */
$resourceGroupResource = $modx->newObject('modResourceGroupResource');
$resourceGroupResource->set('document',$resource->get('id'));
$resourceGroupResource->set('document_group',$resourceGroup->get('id'));

if ($resourceGroupResource->save() == false) {
    return $modx->error->failure($modx->lexicon('resource_group_resource_err_create'));
}

return $modx->error->success('',$resourceGroupResource);