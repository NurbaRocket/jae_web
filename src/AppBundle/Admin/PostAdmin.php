<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\TranslationBundle\Filter\TranslationFieldFilter;

class PostAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'createTime',
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Content', array('class' => 'col-md-9'))
                ->add('title', null, array(
                    'label' => 'label.blog.admin.article_title',
                ))
                ->add('content', CKEditorType::class, array())
            ->end()
            ->with('Meta Data', array('class' => 'col-md-3'))
                ->add('url', null, array(
                ))
                ->add('createTime', null, array(
                ))
                ->add('updateTime', null, array(
                ))
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', TranslationFieldFilter::class)
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('title', null)
            ->add('createTime', null, array());
    }
}