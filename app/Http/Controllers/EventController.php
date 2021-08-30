<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\User;


class EventController extends Controller
{


    public function index() {
        /*busca*/
        $search = request('search');

        if($search) {
            $events = Event::where([
                ['title', 'like', '%'.$search.'%']
            ])->orderBy('date','desc')->get();

        }else{
            $events = Event::all();
        }
        return view('welcome',['events' => $events, 'search' => $search]);
    }

    public function create() {
        return view('events.create');
    }

    public function store(Request $request) {
        $event = new Event;

        $event->title = $request->title;
        $event->date = $request->date;
        $event->city = $request->city;
        $event->private = $request->private;
        $event->description = $request->description;
        $event->items = $request->items;

        // Imagem Upload
        if($request->hasFile('image') && $request->file('image')->isValid()) {
            $requestImage = $request->image;
            $extension = $requestImage->extension();
            //pegando a imagem
            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;
            //salvando no servidor
            $requestImage->move(public_path('img/events'), $imageName);
            $event->image = $imageName;
        }
       //verificando o usuario
        $user = auth()->user();
        $event->user_id = $user->id;

        $event->save();
        return redirect('/')->with('msg', 'Evento criado com sucesso!');

    }

    public function show($id) {

        $event = Event::findOrFail($id);
        $user = auth()->user();
        $TemUser = false;
        if($user){
            $userEvents = $user->eventAsParticipant->toArray();
            foreach( $userEvents as $userEvent){
                if($userEvent['id'] == $id){
                    $TemUser = true;
                }

            }
        }
        $eventOwner = User::where('id', $event->user_id)->first()->toArray();
        return view('events.show', ['event' => $event, 'eventOwner'=>$eventOwner,'TemUser'=>$TemUser]);

    }
    public function dashboard(){
        $user = auth()->user();
        $events = $user->events;
        $eventAsParticipant=$user->eventAsParticipant;
        return view('events.dashboard',['events'=>$events, 'eventAsParticipant'=>$eventAsParticipant]);
    }
    public function destroy($id){
       // Event::findOrFail($id)->delete();
       //DB::table('event_user')->where([['event_id',$id]])->delete();
       DB::delete('delete from event_user where event_id = ?',[$id]);

       DB::delete('delete from events where id = ?',[$id]);
      // DB::table('events')->where([['id',$id]])->delete();

        return redirect('/dashboard')->with('msg','deletado com sucesso');
    }
    public function edit($id){
       $user =auth()->user();
       $event = Event::findOrFail($id);

        if($user->id != $event->user_id){
          return redirect('/dashboard');
        }
      return view('events.edit',['event'=>$event]);
    }
    public function update(request $request){
        $data= $request->all();

        if($request->hasFile('image') && $request->file('image')->isValid()) {
            $requestImage = $request->image;
            $extension = $requestImage->extension();
            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;
            $requestImage->move(public_path('img/events'), $imageName);
            $data['image']= $imageName;
        }

        Event::findOrFail($request->id)->update($data);
        return redirect('/dashboard')->with('msg','evento editado com sucesso');
      }
    public function eventJoin($id){
      $user = auth()->user();
      $user->eventAsParticipant()->attach($id);//insere o id do evento e do usuario
      $event = Event::findOrFail($id);
      return redirect('/dashboard')->with('msg','sua presença está confirmada'.$event->title);
    }

    public function leave($id){
      $user = auth()->user();
      $user->eventAsParticipant()->detach($id);
      $event = Event::findOrFail($id);
      return redirect('/dashboard')->with('msg',' Voce saiu  com sucesso');
    }

}

