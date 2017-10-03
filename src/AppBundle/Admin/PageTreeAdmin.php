<?php
namespace AppBundle\Admin;

use RedCode\TreeBundle\Admin\AbstractTreeAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Sonata\TranslationBundle\Filter\TranslationFieldFilter;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Form\Type\ModelType;

class PageTreeAdmin extends AbstractTreeAdmin
{
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'id',
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Content', array('class' => 'col-md-9'))
                ->add('title', null)
                ->add('status', CheckboxType::class, array(
                    'required' => false
                ))
                ->add('content', CKEditorType::class, array(
                    'required' => false
                ))
            ->end()
            ->with('Meta data', array('class' => 'col-md-3'))
                ->add('url', null,  array(
                    'required' => false,
                ))
                ->add('parent', ModelType::class, array(
                    'required' => false,
                    'class' => 'AppBundle\Entity\PageTree',
                ))
                ->add('pageType', ChoiceType::class, array(
                    'choices' => array(
                        'default_page' => 'default_page',
                        'news_page' => 'gallery_page',
                        'gallery_page' => 'gallery_page',
                    )
                ))
                ->add('metaTitle', TextareaType::class,  array(
                    'required' => false,
                ))
                ->add('metaDescription', TextareaType::class,  array(
                    'required' => false,
                ))
                ->add('metaKeywords', TextareaType::class,  array(
                    'required' => false,
                ))
            ->end()
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', TranslationFieldFilter::class)
        ;
    }
}
