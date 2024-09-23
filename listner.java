import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.statemachine.listener.StateMachineListenerAdapter;
import org.springframework.statemachine.state.State;
import org.springframework.stereotype.Component;

@Component
public class StateMachineListener extends StateMachineListenerAdapter<String, String> {

    @Autowired
    private UserRepository userRepository;

    @Autowired
    private StateRepository stateRepository;

    // Inject userId dynamically
    private Long userId;

    public void setUserId(Long userId) {
        this.userId = userId;
    }

    @Override
    public void stateChanged(State<String, String> from, State<String, String> to) {
        if (to != null && userId != null) {
            // Get the user from the DB using the dynamic userId
            Optional<User> userOpt = userRepository.findById(userId);
            if (userOpt.isPresent()) {
                User user = userOpt.get();

                // Get the new state from the DB based on the state name
                State newState = stateRepository.findByName(to.getId());

                if (newState != null) {
                    user.setState(newState);  // Update user state
                    userRepository.save(user);  // Persist the change
                    System.out.println("User state updated to: " + newState.getName());
                } else {
                    System.out.println("State not found in the database.");
                }
            } else {
                System.out.println("User not found.");
            }
        }
    }
}