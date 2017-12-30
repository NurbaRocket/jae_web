<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\TranslationBundle\Filter\TranslationFieldFilter;

class BlogAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'createTime',
    );

    protected function configureFormFields(FormMapper $formMapper)
    {

        $em = $this->modelManager->getEntityManager('AppBundle\Entity\PageTree');

        $query = $em->createQueryBuilder('p')
            ->select('p')
            ->from('AppBundle:PageTree', 'p')
            ->where('p.pageType = :type')
            ->setParameter('type', 'news_page')
        ;
        $formMapper
            ->with('Content', array('class' => 'col-md-9'))
                ->add('title', null, array(
                    'label' => 'label.blog.admin.article_title',
                ))
                ->add('media', 'sonata_type_model_list', array(), array(
                    'link_parameters' => array('context' => 'news')
                ))
                ->add('content', CKEditorType::class, array())
            ->end()
            ->with('Meta Data', array('class' => 'col-md-3'))
                ->add('tags', null, array(
                    'label' => 'label.blog.admin.tags'
                ))
                ->add('status', null, array(
                    'label' => 'label.blog.admin.status'
                ))
                ->add('pageTree', ModelType::class, array(
                    'label' => 'label.blog.admin.pageTree',
                    'required' => false,
                    'query' => $query,
                    //'class' => 'AppBundle\Entity\PageTree',
                ))
                ->add('createTime', null, array(
                    'label' => 'label.blog.admin.create_time'
                ))
                ->add('updateTime', null, array(
                    'label' => 'label.blog.admin.update_time'
                ))
            ->end()
            ->with('Photo reports', array('class' => 'col-md-9'))
                ->add('photoReports', 'sonata_type_collection', array(
                    'by_reference' => false,
                    'type_options' => array(
                        // Prevents the "Delete" option from being displayed
                        'delete' => false,
                        'delete_options' => array(
                            // You may otherwise choose to put the field but hide it
                            'type'         => 'hidden',
                            // In that case, you need to fill in the options as well
                            'type_options' => array(
                                'mapped'   => false,
                                'required' => false,
                            )
                        )
                    )
                ), array(
                    'link_parameters' => array('context' => 'news'),
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'position'
                ))
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title', TranslationFieldFilter::class, array(
            'label' => 'label.blog.admin.article_title'
        ))->add('pageTree');
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
            ));
    }
}
