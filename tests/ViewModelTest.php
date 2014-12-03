<?php

use OsiemSiedem\View\ViewModel;

class ViewModelTest extends PHPUnit_Framework_TestCase {

	public function testViewModelGettersSetters()
	{
		$viewmodel = $this->getViewModel();

		// toArray()
		$this->assertInstanceOf('Illuminate\Contracts\Support\Arrayable', $viewmodel);
		$this->assertEquals($viewmodel->toArray(), ['foo' => 'bar', 'bar' => 'foo']);

		// toJson()
		$this->assertInstanceOf('Illuminate\Contracts\Support\Jsonable', $viewmodel);
		$this->assertEquals($viewmodel->toJson(), '{"foo":"bar","bar":"foo"}');

		// jsonSerialize()
		$this->assertInstanceOf('JsonSerializable', $viewmodel);
		$this->assertEquals($viewmodel->jsonSerialize(), ['foo' => 'bar', 'bar' => 'foo']);

		// has()
		$this->assertTrue($viewmodel->has('foo'));
		$this->assertFalse($viewmodel->has('xyz'));
		$this->assertFalse($viewmodel->has('hidden'));

		// get()
		$this->assertEquals($viewmodel->get('foo'), 'bar');
		$this->assertEquals($viewmodel->get('bar'), 'foo');
		$this->assertEquals($viewmodel->get('hidden'), null);

		// set()
		$viewmodel->set('xyz', '123');
		$this->assertTrue($viewmodel->has('xyz'));
		$this->assertEquals($viewmodel->get('xyz'), '123');

		$viewmodel->set(['a' => '1', 'b' => '2']);
		$this->assertEquals($viewmodel->get('a'), '1');
		$this->assertEquals($viewmodel->get('b'), '2');

		// forget()
		$viewmodel->forget('xyz');
		$this->assertFalse($viewmodel->has('xyz'));
		$this->assertEquals($viewmodel->get('xyz'), null);
	}

	public function testViewModelArrayAccess()
	{
		$viewmodel = $this->getViewModel();

		$this->assertInstanceOf('ArrayAccess', $viewmodel);

		// offsetExists() / offsetGet()
		$this->assertTrue($viewmodel->offsetExists('foo'));
		$this->assertEquals($viewmodel->offsetGet('foo'), 'bar');

		// offsetSet() / offsetGet()
		$viewmodel->offsetSet('foo', 'baz');
		$this->assertEquals($viewmodel->offsetGet('foo'), 'baz');

		// offsetUnset()
		$viewmodel->offsetUnset('foo');
		$this->assertFalse($viewmodel->offsetExists('foo'));
	}

	public function testViewModelMagicMethods()
	{
		$viewmodel = $this->getViewModel();

		// __isset() / __get()
		$this->assertTrue(isset($viewmodel->foo));
		$this->assertEquals($viewmodel->foo, 'bar');

		$this->assertTrue(isset($viewmodel->bar));
		$this->assertEquals($viewmodel->bar, 'foo');

		// __toString()
		$this->assertEquals( (string) $viewmodel, '{"foo":"bar","bar":"foo"}');

		// __construct()
		$viewmodel = $this->getViewModel(['a' => 'b']);

		$this->assertEquals($viewmodel->a, 'b');
	}

	protected function getViewModel(array $data = [])
	{
		return new TestViewModel($data);
	}

}

class TestViewModel extends ViewModel {

	public $foo = 'bar';

	protected $hidden = 'qwerty';

	public function bar()
	{
		return 'foo';
	}

}
