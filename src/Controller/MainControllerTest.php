use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class MainControllerTest extends WebTestCase
{
    public function testAddTask()
    {
        $client = static::createClient();

        // submit a form with data
        $crawler = $client->request('GET', '/add-task');
        $form = $crawler->selectButton('Save')->form();
        $form['post[title]'] = 'Test Task';
        $form['post[description]'] = 'This is a test task';
        $client->submit($form);

        // assert that the task was added successfully
        $this->assertTrue($client->getResponse()->isRedirect('/app_main'));
    }
}