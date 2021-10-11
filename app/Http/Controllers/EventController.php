<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Banco
use App\Models\Event;

// dono do evento
use APP\Models\User;

class EventController extends Controller
{
    
    public function index() {

        // sistema de busca
        $search = request('search');

        if($search) {

            $events = Event::where([
                ['title', 'like', '%'.$search.'%']
            ])->get();

        } else {

             // Pega todos os registros
            $events = Event::all();
        }

        return view('welcome', ['events' => $events, 'search' => $search]);
    }

    public function create() {
        return view('events.create');
    }

    // Cadastrar no banco via POST
    public function store(Request $request) {

        $event = new Event;

        $event->title = $request->title;
        $event->date = $request->date;
        $event->city = $request->city;
        $event->private = $request->private;
        $event->description = $request->description;
        $event->items = $request->items;

        // image upload
        if($request->hasFile('image') && $request->file('image')->isValid()) {

            $requestImage = $request->image;

            $extension = $requestImage->extension();

            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;

            $requestImage->move(public_path('img/events'), $imageName);

            $event->image = $imageName;

        }

        // Preencher user_id
        $user = auth()->user();
        $event->user_id = $user->id;

        // save dates
        $event->save();

        return redirect('/')->with('msg', 'Evento criado com sucesso!');

    }

    public function show($id) {

        $event = Event::findOrFail($id);

        // Vereficiar se o usuario ja marcou sua presença para não deixar ele participar de dois eventos ao mesmo tempo
        $user = auth()->user();
        $hasUserJoined = false;

        if($user) {

            $userEvents = $user->eventsAsParticipants->toArray();

            foreach($userEvents as $userEvent) {
                if($userEvent['id'] == $id) {
                    $hasUserJoined = true;
                }
            }
            
        }

        // Caso o usuario tenha esteja validado ele entra SE NÂO ele vai pra pagina de cadastro
        if($user = auth()->user()){
            // Mostar nome do dono do evento
            $eventOwner = user::where('id', $event->user_id)->first()->toArray();

            return view('events.show', ['event' => $event, 'eventOwner' => $eventOwner, 'hasUserJoined' => $hasUserJoined]);

        } else {
            return view('auth.register');
        }
        

    }

    public function dashboard() {

        $user = auth()->user();

        $events = $user->events;

        $eventsAsParticipant = $user->eventsAsParticipants;

        return view('events.dashboard', ['events' => $events, 'eventsasparticipant' => $eventsAsParticipant]);

    }

    // Delete
    public function destroy($id) {

        Event::findOrFail($id)->delete();

        return redirect('/dashboard')->with('msg', 'Evento excluído com sucesso!');
    }

    // Update
    public function edit($id) {

        // Impedir que o usuario edite um evento que não é seu
        $user = auth()->user();

        $event = Event::findOrFail($id);

        // Impedir que o usuario edite um evento que não é seu
        if($user->id != $event->user_id) {
            return redirect('/dashboard');
        }

        return view('events.edit', ['event' => $event]);

    }

    // Update
    public function update(Request $request) {

        $data = $request->all();
        // image
        if($request->hasFile('image') && $request->file('image')->isValid()) {

            $requestImage = $request->image;

            $extension = $requestImage->extension();

            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;

            $requestImage->move(public_path('img/events'), $imageName);

            $data['image'] = $imageName;

        }

        Event::findOrFail($request->id)->update($data);

        return redirect('/dashboard')->with('msg', 'Evento atualizado com sucesso!');

    }

    public function joinEvent($id) {

        $user = auth()->user();

        $user->eventsAsParticipants()->attach($id);

        $event = Event::findOrFail($id);

        return redirect('/dashboard')->with('msg', 'Sua presença foi confirmada com sucesso no evento '. $event->title);

    }

    // remover presença do usuario
    public function leaveEvent($id) {

        $user = auth()->user();

        $user->eventsAsParticipants()->detach($id);

        $event = Event::findOrFail($id);

        return redirect('/dashboard')->with('msg', 'Você saiu com sucesso do evento: '. $event->title);

    }
}