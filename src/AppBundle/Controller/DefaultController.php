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

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }
    
    /**
     * @Route("/admin", name="admin")
     */
    public function adminAction()
    {
        return new Response('<html><body>Página Admin</body></html>');    
    }
    
    /**
     * @Route("departamento/inicio", name="departamento_inicio")
     */
    public function inicioAction()
    {
        $em = $this->getDoctrine()->getManager();
        $departamentos = $em->getRepository(Departamento::class)->findAll();
        //return $this->render('departamento/inicio.html.twig', array('departamentos' => $aulas,));
        return $this->render('departamento/inicio.html.twig');
    }
    
    /**
     * @Route("departamento/nuevo", name="departamento_nuevo")
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
            // Sacamos la extensión del fichero
            $ext=$file->guessExtension();
            // Le ponemos un nombre al fichero
            $fileName=time().".".$ext;
            // Guardamos el fichero en el directorio creado, que estará en el directorio /web del framework
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
}
