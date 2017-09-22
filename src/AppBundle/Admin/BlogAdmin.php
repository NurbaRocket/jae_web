<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;


class BlogAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
        ->with('Content', array('class' => 'col-md-9'))
          ->add('title', null, array(
            'label' => 'label.blog.admin.article_title',
          ))
          ->add('content', CKEditorType::class, array(
          ))
        ->end()
        ->with('Meta Data', array('class' => 'col-md-3'))
        ->add('tags', null, array(
          'label' => 'label.blog.admin.tags'
        ))
        ->add('status', null, array(
          'label' => 'label.blog.admin.status'
        ))
        ->add('createTime', null, array(
          'label' => 'label.blog.admin.create_time'
        ))
        ->add('updateTime', null, array(
          'label' => 'label.blog.admin.update_time'
        ))
        ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title', null, array (
          'label' => 'label.blog.admin.article_title'
        ));
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('title', null, array(
          'label' => 'label.blog.admin.article_title',))
          ->add('status', null, array(
            'label' => 'label.blog.admin.status'
          ))
          ->add('createTime', null, array(
            'label' => 'label.blog.admin.create_time'
          ))

          ;
    }
}
