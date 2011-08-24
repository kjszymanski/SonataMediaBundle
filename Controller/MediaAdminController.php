<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\MediaBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MediaAdminController extends Controller
{
    /**
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException|\Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @param $id
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function viewAction($id)
    {
        if (false === $this->admin->isGranted('VIEW')) {
            throw new AccessDeniedException();
        }

        $media = $this->get('sonata.media.manager.media')->findOneBy(array('id' => $id));

        if (!$media) {
            throw new NotFoundHttpException('unable to find the media with the id');
        }

        return $this->render('SonataMediaBundle:MediaAdmin:view.html.twig', array(
            'media'         => $media,
            'formats'       => $this->get('sonata.media.pool')->getFormatNamesByContext($media->getContext()),
            'format'        => $this->get('request')->get('format', 'reference'),
            'base_template' => $this->getBaseTemplate(),
            'admin'         => $this->admin,
            'action'        => 'view'
        ));
    }

    /**
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }

        $parameters = $this->admin->getPersistentParameters();

        if (!$parameters['provider']) {
            return $this->render('SonataMediaBundle:MediaAdmin:select_provider.html.twig', array(
                'providers'     => $this->get('sonata.media.pool')->getProvidersByContext($this->get('request')->get('context', 'default')),
                'base_template' => $this->getBaseTemplate(),
                'admin'         => $this->admin,
                'action'        => 'create'
            ));
        }

        return parent::createAction();
    }
}