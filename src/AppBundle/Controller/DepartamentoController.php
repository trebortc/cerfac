<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use AppBundle\Entity\Departamento;
use AppBundle\Form\DepartamentoType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/admin")
 * @Security("has_role('ROLE_ADMIN')")
 */
class DepartamentoController extends Controller
{ 

    /**
     * @Route("/departamento/inicio", name="departamento_inicio")
     */
    public function inicioAction()
    {
        $em = $this->getDoctrine()->getManager();
        $departamentos = $em->getRepository(Departamento::class)->findAll();
        return $this->render('departamento/inicio.html.twig', array('departamentos' => $departamentos,));
    }
    
    /**
     * @Route("/departamento/nuevo", name="departamento_nuevo")
     */
    public function nuevoAction(Request $request)
    {
        $departamento = new Departamento();
        $form = $this->createForm(DepartamentoType::class,$departamento);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            // Recogemos el fichero
            $file = $departamento->getImagen();
            // Sacamos la extensi�n del fichero
            $ext=$file->guessExtension();
            // Le ponemos un nombre al fichero
            $fileName=time().".".$ext;
            // Guardamos el fichero en el directorio creado, que estar� en el directorio /web del framework
            $file->move(
                $this->getParameter('departamentos_directorio'),
                $fileName
            );
            
            // Establecemos el nombre de fichero en el atributo de la entidad
            $departamento->setImagen($fileName);
            $em->persist($departamento);
            $em->flush();
            return $this->redirect($this->generateUrl('departamento_inicio'));
        }
        return $this->render('departamento/nuevo.html.twig',array('form' => $form->createView()));
    }

    /**
     * @Route("/departamento/nuevo", name="departamento_editar")
     */
    public function editarAction(Request $request)
    {

    }

    /**
     * @Route("/departamento/nuevo", name="departamento_eliminar")
     */
    public function eliminarAction(Request $request)
    {

    }
}
